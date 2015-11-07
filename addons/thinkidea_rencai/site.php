<?php
/**
 * 微招聘模块微站定义
 *
 * 
 */

defined('IN_IA') or exit('Access Denied');
session_start();

class Thinkidea_rencaiModuleSite extends WeModuleSite {
	
	//用户表
	private $member_table = 'thinkidea_rencai_member';

	//企业注册表
	private $company_table = 'thinkidea_rencai_company';

    //职位表
	private $job_table = 'thinkidea_rencai_job';

	//职位分类表
	private $category_table = 'thinkidea_rencai_category';
	
	//行业分类表
	private $industry_table = 'thinkidea_rencai_industry';
	
	//自定义分享标题描述
	private $share_table = 'thinkidea_rencai_share';

	//求职者-基础表
	private $person_table = 'thinkidea_rencai_person';
	
	//求职者-简历表
	private $resume_table = 'thinkidea_rencai_person_resume';
	
	//收藏表
	private $collect_table = 'thinkidea_rencai_person_collect';
	
	//职位申请表
	private $apply_table = 'thinkidea_rencai_apply_jobs';
	
	//幻灯图片广告表
	private $ads_table = 'thinkidea_rencai_adslider';

    //职位评论表
    private $jobs_comments_table = 'thinkidea_rencai_jobs_comments';
	
	//cookie有效期7天
	private static $COOKIE_DAYS = 7;
	
	//weid
	public $weid;

	//from_user
    public $from_user;

    //自定义分享
    public $SHARE;

	public function __construct(){
		global $_W;
		$oauth_openid = "ThinkIdea_rencai_".$_W['uniacid'];
		$this->weid = $_W['weid'];
		$this->from_user = $_W['openid']; //$_W['openid'];当前粉丝用户信息
		//定义分享数据
		$_W['mobile']['share'] = pdo_fetch("SELECT * FROM ".tablename($this->share_table)." WHERE uniacid = :uniacid LIMIT 1", array('uniacid' => $this->weid));
        
	}

	//===============================================Mobile=========================================
	/**
	 * 首页
	 */
	public function doMobileIndex(){
		global $_GPC,$_W;

        $config = $this->get_config();
        $nowtime = time();
		//======幻灯AD======
		$ad_lists = pdo_fetchall("SELECT * FROM ".tablename($this->ads_table)." WHERE weid = :weid AND isshow = 1 AND exprtime > :time ORDER BY display ASC ",array(":weid" => $this->weid, ":time" => $nowtime));
		$big_ad_lists = $small_ad_lists = array();
		foreach ($ad_lists AS $key => $ad){
			if ($ad['position'] == 1){
				array_unshift($big_ad_lists, $ad);
			}else {
				array_unshift($small_ad_lists, $ad);
			}
		}
        $big_ad_nums = count($big_ad_lists);

		//======置顶======
		$time = time();
        $offset = intval($this->module['config']['indextopnums']);
		$top_lists = pdo_fetchall("SELECT * FROM ".tablename($this->job_table)." WHERE weid = :weid AND istop = 1 AND expiration > {$time} LIMIT 0, {$offset} ",array(":weid" => $this->weid));
		//置顶信息背后的企业信息
		$tmp = array();
		foreach ($top_lists AS $key => $val){
			array_unshift($tmp, $val['mid']);
			$top_lists[$key]['welfare'] = explode(',', $val['welfare']);
		}
		if(!empty($tmp)){
            $top_companys = $this->get_companys_info($tmp);
		}
        unset($tmp);

		//======最新招聘=====
        $limit = intval($this->module['config']['indexlastnums']);
        $last_tmp_jobs = pdo_fetchall("SELECT id,mid,title,payroll,welfare FROM ".tablename($this->job_table)." WHERE weid = :weid ORDER BY dateline DESC limit 0, {$limit} ",array(":weid" => $this->weid));
        //最新招聘背后的企业
        $tmp = $last_jobs = array();
        foreach ($last_tmp_jobs AS $key => $val){
            array_unshift($tmp, $val['mid']);
            $last_jobs[$key] = $val;
            $last_jobs[$key]['welfare'] = explode(',', $val['welfare']);
        }
        $tmp = array_unique($tmp);  //去重
        if(!empty($tmp)){
            $last_companys = $this->get_companys_info($tmp);
        }
        unset($tmp);

		//========热门职位==========
        $limit = intval($this->module['config']['indexhotnums']);
		$hot_jobs = pdo_fetchall("SELECT * FROM ".tablename($this->job_table)." WHERE weid = :weid AND ishot = 1 ORDER BY dateline DESC LIMIT 0, {$limit} ", array(":weid" => $this->weid));
        //热门信息背后的企业
        $tmp = array();
        foreach ($hot_jobs AS $key => $val){
            array_unshift($tmp, $val['mid']);
            $hot_jobs[$key]['welfare'] = explode(',', $val['welfare']);
        }
        if(!empty($tmp)){
            $hot_companys = $this->get_companys_info($tmp);
        }
        //名企推荐
        $limit = intval($this->module['config']['indexcompanynums']);
        $companys_positions = pdo_fetchall("SELECT `id`, `name`, `logo` FROM ".tablename($this->company_table)." WHERE weid = :weid AND position = 1 LIMIT 0, {$limit}", array(":weid" => $this->weid));
        //附件地址
        $atturl = $_W['attachurl'].'thinkidea_rencai/';
        //是否开启热门职位推荐
        $isopenindexhot = $this->module['config']['isopenindexhot'];
        //标题
        $title = '微人才';
		include $this->template('home_index');
	}
	

	/**
	 * 招聘列表【废弃】
	 */
	public function doMobileJobIndex(){
		global $_W, $_GPC;

        //最新加入的公司
        $last_company = pdo_fetchall("SELECT * FROM ".tablename($this->company_table)." WHERE weid = :weid AND status = 1 ORDER BY id DESC LIMIT 20", array(":weid" => $this->weid));
        foreach($last_company AS $key => $val){
            $last_company[$key]['dateline'] = date("Y-m-d", $val['dateline']);
        }

		//取父类
		$parent_cate = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id = 0 AND isshow = 1 ORDER BY display ASC",array(":weid" => $this->weid));
		$tmp = array();
		foreach ($parent_cate AS $parent){
			array_push($tmp, $parent['id']);
		}
		$tmp = implode(",", $tmp);
		//取子类
		$sub_cate = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id IN (". $tmp .") AND isshow = 1",array(":weid" => $this->weid));
		foreach ($parent_cate AS $key => $parent){
			$i = 1;
			foreach ($sub_cate AS $k => $sub){
				if($sub['parent_id'] == $parent['id']){
					$parent_cate[$key]['sub'][$i] = $sub;
					$i++;
				}
			}
		}

		include $this->template('job_index');
	}
	
	
	/**
	 * 职位列表
	 */
	public function doMobileJobList(){
		global $_W, $_GPC;
		$config = $this->get_config();
        $offices = $this->get_all_office();
        $where = empty($_GPC['cid']) ? '' : ' AND cid = '.intval($_GPC['cid']);
        if(!empty($_GPC['cid'])){
            $cid = intval($_GPC['cid']);
            $cname = pdo_fetchcolumn("SELECT `name` FROM ".tablename($this->category_table)." WHERE id = ".$_GPC['cid']." LIMIT 1");
        }else{
            $cname = '全部职位';
        }
		//查询条件
		//*****************************搜索框****************************
		//关键词搜索
		if(isset($_GPC['keyword'])){
			$keyword = trim($_GPC['keyword']);
			$where .= ' AND title LIKE \'%'.$keyword.'%\'';
		}
		//****************************列表页****************************
		//薪资
		if(isset($_GPC['payroll'])){
			$query_payroll = intval($_GPC['payroll']);
			$where .= ' AND payroll = '.$query_payroll;
		}
		
		//类型
		if(isset($_GPC['positiontype'])){
			$query_positiontype =  intval($_GPC['positiontype']);
			$where .= ' AND positiontype = '.$query_positiontype;
		}
		//**************************************************************

//        exit($where);

		//取所有栏目下职位信息，左查询
//		$job_lists = pdo_fetchall("SELECT j.id AS job_id, j.title AS job_title, c.name AS company_name, c.isauth AS company_isauth, j.payroll AS job_payroll FROM ".tablename($this->job_table)." AS j LEFT JOIN ".tablename($this->company_table)." AS c ON j.mid = c.id WHERE j.weid = c.weid AND c.weid = :weid AND c.status = 1 AND j.cid IN (:cids) ".$where, array(":weid" => $this->weid, ":cids" => $cids));

        $job_lists = pdo_fetchall("SELECT * FROM ".tablename($this->job_table)." WHERE weid = :weid $where", array(":weid" => $this->weid));
        if(!empty($job_lists)) {
            $companyids = array();
            foreach ($job_lists AS $key => $val) {
                array_unshift($companyids, $val['mid']);
                $job_lists[$key]['welfare'] = explode(',', $val['welfare']);
            }
            $companyids = implode(',', array_unique($companyids));
            $tmp = pdo_fetchall("SELECT `id`,`name`,`isauth` FROM " . tablename($this->company_table) . " WHERE id IN (" . $companyids . ")");
            $companys = array();
            foreach ($tmp AS $key => $val) {
                $companys[$val['id']] = $val;
            }
            unset($tmp);
        }
        $title = '招聘频道';
		include $this->template('job_list');
	}


	/**
	 * 职位信息
	 */
	public function doMobileJobShow(){
		global $_W, $_GPC;
		$config = $this->get_config();
		$job_id = intval($_GPC['job_id']);

		//职位信息
		$job = pdo_fetch("SELECT * FROM ".tablename($this->job_table) ." WHERE id = :id", array(":id" => $job_id));
		$job['dateline'] = date("Y-m-d", $job['dateline']);
        $is_has_welfare = empty($job['welfare']) ? false : true;    //是否有福利
		$job['welfare'] = explode(',', $job['welfare']);

		//对应公司信息
		$company = pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE id = :mid", array(":mid" => $job['mid']));
		//取职位分类
		$category = pdo_fetch("SELECT name FROM ".tablename($this->category_table)." WHERE id = :id AND weid = :weid LIMIT 1", array(":id" => $job['cid'], ":weid" => $this->weid));
	
		//取公司所属行业
		$industry = pdo_fetch("SELECT name FROM ".tablename($this->industry_table)." WHERE id = :id AND weid = :weid LIMIT 1", array(":id" => $company['industry'], ":weid" => $this->weid));

		//更新浏览次数
		pdo_update($this->job_table, array('views' => $job['views'] + 1), array('id' => $job_id));

		$uid = $this->get_member_id();
        //是否申请
        $isapply = $this->get_is_apply($uid, $job_id);

		//是否收藏
        $iscollect = $this->get_is_collect($uid, $job_id);

        //评论
        $comments = pdo_fetchall("SELECT * FROM ".tablename($this->jobs_comments_table)." WHERE weid = :weid AND jobid = :jobid AND status = 1 ORDER BY dateline DESC LIMIT 2", array(":weid" => $this->weid, ":jobid" => $job_id));
        $tmp = array();
        foreach($comments AS $key => $val){
            array_unshift($tmp, $val['mid']);
            $comments[$key]['dateline'] = date("Y-m-d", $val['dateline']);
        }
        if(!empty($tmp)){
            $persons = $this->get_person_info($tmp);
            foreach($persons AS $key => $person){
                $persons[$key]['headimgurl'] = empty($person['headimgurl']) ? ($person['sex'] == 1 ? $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_man.jpg' : $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_woman.jpg' ) : $_W['attachurl'].'thinkidea_rencai/avatar/'.$person['headimgurl'];
             }
        }
        $title = '职位详情';
        include $this->template('job_show');
	}


    /**
     * 公司概况
     */
    public function doMobileCompanyShow(){
        global $_W, $_GPC;
        $config = $this->get_config();
        $companyid = intval($_GPC['companyid']);
        $company = pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE id = :id LIMIT 1", array(":id" => $companyid));
        //取公司所属行业
        $industry = pdo_fetch("SELECT name FROM ".tablename($this->industry_table)." WHERE id = :id AND weid = :weid LIMIT 1", array(":id" => $company['industry'], ":weid" => $this->weid));
        //改公司其他职位
        $other_jobs = pdo_fetchall("SELECT * FROM ".tablename($this->job_table)." WHERE weid = :weid AND mid = :mid", array(":weid" => $this->weid, ":mid" => intval($company['id'])));

        $title = '公司概况';
        include $this->template('company_show');
    }

    /**
	 * 求职列表
	 */
	public function doMobileResume(){
		global $_W, $_GPC;
        $config = $this->get_config();
		$time = time();
		//置顶
		$top_lists = pdo_fetchall("SELECT * FROM ".tablename($this->person_table)." WHERE weid = :weid AND istop = 1 AND expiration > {$time}", array(":weid" => $this->weid));
		foreach ($top_lists AS $key => $val){
			$top_lists[$key]['headimgurl'] = empty($val['headimgurl']) ? ($val['sex'] == 1 ? $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_man.jpg' : $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_woman.jpg' ) : $_W['attachurl'].'thinkidea_rencai/avatar/'.$val['headimgurl'];
			$top_lists[$key]['dateline'] = date("Y-m-d", $val['dateline']);
			$top_lists[$key]['updatetime'] = date("Y-m-d", $val['updatetime']);
		}
		//普通
		$lists = pdo_fetchall("SELECT * FROM ".tablename($this->person_table)." WHERE weid = :weid AND istop = 0 ", array(":weid" => $this->weid));
		foreach ($lists AS $key => $val){
			$lists[$key]['headimgurl'] = empty($val['headimgurl']) ? ($val['sex'] == 1 ? $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_man.jpg' : $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_woman.jpg' ) : $_W['attachurl'].'thinkidea_rencai/avatar/'.$val['headimgurl'];
			$lists[$key]['dateline'] = date("Y-m-d", $val['dateline']);
			$lists[$key]['updatetime'] = date("Y-m-d", $val['updatetime']);
		}

        $title = '求职频道';
		include $this->template('home_resume');
	}

    /**
     * 评论AJAX
     */
    public function doMobileCommentAjax(){
        global $_W, $_GPC;
        //判断是否注册
        if(false == $type = $this->get_memner_type()){
            exit('-2');
        }
        if($type == 1){
            exit('-3');
        }
        $data = array(
            'weid' => $this->weid,
            'mid' => intval($_GPC['mid']),
            'jobid' => intval($_GPC['jobid']),
            'content' => $_GPC['content'],
            'status' => 1,
            'dateline' => time()
        );
        if(pdo_insert($this->jobs_comments_table, $data)){
            echo('1');
        }else{
            echo('-1');
        }
    }

    //***************************************************************************
    //
    //                                用户中心
    //
    //****************************************************************************
    /**
     * 我的
     */
    public function doMobileMy(){
        global $_W, $_GPC;
        //是否关注
        $oauth_openid="ThinkIdea_rencai_".$_W['uniacid'];
        if(empty($_COOKIE[$oauth_openid])){
            $this->getCode();
        }
        $this->getFollow();

        //检查是个人:2,还是企业:1
        $type = pdo_fetchcolumn("SELECT `type` FROM ".tablename($this->member_table)." WHERE weid = :weid AND from_user = :from_user AND status = 1 LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
        if(false == $type){
            $this->doMobilePublicIndex();
        }else{
            if($type == 1){
                $this->doMobileMyCompanyIndex();
            }else{
                $this->doMobileMyPersonIndex();
            }
        }
    }


    /**
     * 企业用户-用户中心首页
     */
    public function doMobileMyCompanyIndex(){
        global $_W, $_GPC;
        $company = pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
        $ret = pdo_fetch("SELECT mid, COUNT(*) AS nums FROM ".tablename($this->job_table)." WHERE weid = :weid AND mid = :mid", array(":weid" => $this->weid, ":mid" => $company['id']));
        //认证的联系电话
        $telephone = $this->module['config']['telephone'];
        $title = '用户中心';
        include $this->template('my_company_index');
    }


    /**
     * 求职者-用户中心首页
     */
    public function doMobileMyPersonIndex(){
        global $_W, $_GPC;
        $person = pdo_fetch("SELECT * FROM ".tablename($this->person_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
        //头像
        $person['headimgurl'] = empty($person['headimgurl']) ? ($person['sex'] == 1 ? $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_man.jpg' : $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_woman.jpg' ) : $_W['attachurl'].'thinkidea_rencai/avatar/'.$person['headimgurl'];
        //电话
        $telephone = $this->module['config']['telephone'];
        $title = '用户中心';
        include $this->template('my_person_index');
    }


    /**
     * 新用户-注册类型
     */
    public function doMobilePublicIndex(){
        global $_GPC;
        $title = '选择发布类型';
        $type = $_GPC['type'];
        include $this->template('public_index');
    }


    /**
     * 用户关注
     */
    public function doMobileFansUs(){
        global $_W, $_GPC;
        $serverapp = $_W['account']['level'];
        if($serverapp != 4){
            $keywords = pdo_fetchall("SELECT content FROM ".tablename('rule_keyword')." WHERE uniacid = :uniacid AND module = 'thinkidea_rencai'", array(":uniacid" => $this->weid));
            $tmp = array();
            foreach ($keywords AS $key){
                array_unshift($tmp, $key['content']);
            }
            $keywords = implode('&nbsp;&nbsp;', $tmp);
            $message = '并发送关键字：<font color="red"><strong>'.$keywords.'</strong></font>';
        }
        $qrcode = $this->module['config']['qrcode'];
        $title = '关注我们';
        include $this->template('fans_us');
    }


    //=====================企业==================
	/**
	* 企业招人
	*/
	public function doMobilePublicJob(){
		global $_W, $_GPC;
        $config = $this->get_config();
		//判断是否已注册
		$company = pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
        if($company !== false && $company['status'] == 0){
            message('您未通过审核，暂不能发布职位', 'referer', 'error');
        }
		//企业id
		$mid = intval($company['id']);	
		if(checksubmit('savejobsubmit')){
			$current_time = time();
            //判断是否填充
            if(empty($_GPC['data']['title'])){
                message("请填写职位名称", 'referer', 'error');
            }
            if(empty($_GPC['data']['nums'])){
                message("请填写招聘人数", 'referer', 'error');
            }
            if(empty($_GPC['data']['workaddress'])){
                message("请填写工作地点", 'referer', 'error');
            }
            if(empty($_GPC['data']['description'])){
                message("请填写职位简介",'referer', 'error');
            }
            if(false == $company){
                if(empty($_GPC['data2']['name'])){
                    message("请填写公司名称", 'referer', 'error');
                }
                if(empty($_GPC['data2']['contact'])){
                    message("请填写联系人", 'referer', 'error');
                }
                if(empty($_GPC['data2']['mobile'])){
                    message("请填写联系电话", 'referer', 'error');
                }
            }
			/**
			 * 只有第一次用户发布职位的时候，注册到用户表、企业表
			 * 下次发布职位的时候，仅注册职位信息
			 */
			if ($company == false){
				//===============插入用户表=================
				$member_insert = array(
						'weid' => $this->weid,
						'from_user' => $this->from_user,
						'type' => 1
				);
				pdo_insert($this->member_table, $member_insert);
				//==============插入企业表==================
				$company_insert = array(
					'weid' => $this->weid,
					'from_user' => $this->from_user,
					'scale' => 0,	//规模
					'status' =>  $this->module['config']['isopenaudit'] ,	//直接通过OR待审核
					'isauth' => 0,
					'dateline' => $current_time,
					'view_resume_total' => $this->module['config']['viewresumenums']
				);
				$company_insert = array_merge($company_insert, $_GPC['data2']);
				pdo_insert($this->company_table, $company_insert);
				//如果是第1次注册
				$mid = pdo_insertid();
			}
			//============插入职位信息=================
			$job_insert = array(
					'weid' => $this->weid,
					'mid' => $mid,
					'dateline' => $current_time,
			);
			$job_insert = array_merge($job_insert, $_GPC['data']);
			$job_insert['welfare'] = strrev(substr(strrev($_GPC['data']['welfare']), 1));	//处理福利id串末尾逗号
			
			if(pdo_insert($this->job_table, $job_insert)){
				message("发布成功", $this->createMobileUrl('My'), 'success');
			}else {
				message("发布失败", $this->createMobileUrl('My'), 'error');
			}
		}else{
            $parent_cate = $this->get_all_office();
            $parent_industry = $this->get_all_industry();
            $title = '发布职位';
			include $this->template('public_job');
		}
	}
	

    /**
     * 我发布的职位
     */
    public function doMobileMyPublicJob(){
        global $_W, $_GPC;
        $config = $this->get_config();
		$companyid = intval($_GPC['companyid']);
        //取发布的职位
        $job_lists = pdo_fetchall("SELECT * FROM ".tablename($this->job_table)." WHERE weid = :weid AND mid = :companyid ORDER BY dateline DESC", array(":weid" => $this->weid, ":companyid" => $companyid));
		foreach ($job_lists AS $key => $job){
			$job_lists[$key]['dateline'] = date("Y/m/d", $job['dateline']);
		}
        $title = '我发布的职位';
        include $this->template('my_company_job');
    }


    /**
     * 编辑职位
     */
    public function doMobileEditJob(){
        global $_W, $_GPC;
        $config = $this->get_config();
        $id = intval($_GPC['id']);
        if(checksubmit('savejobsubmit')){
            $companyid = $_GPC['companyid'];
            $data = $_GPC['data'];
            if(pdo_update($this->job_table, $data, array('id' => $_GPC['jobid']))){
                message("更新成功", $this->createMobileUrl('MyPublicJob', array('companyid' => $companyid)), 'success');
            }else{
                message("更新失败", $this->createMobileUrl('MyPublicJob'), 'error');
            }

        }else{
            $info = pdo_fetch("SELECT * FROM ".tablename($this->job_table)." WHERE id = :id LIMIT 1", array(':id' => $id));
            $welfare_array = explode(',', $info['welfare']);
            //================================取所有职位分类====================
            $parent_cate = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id = 0 AND isshow = 1 ORDER BY display ASC", array(":weid" => $this->weid));
            $tmp = array();
            foreach ($parent_cate AS $parent){
                array_push($tmp, $parent['id']);
            }
            $tmp = implode(",", $tmp);
            $sub_cate = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id IN (". $tmp .") AND isshow = 1 ORDER BY display ASC", array(":weid" => $this->weid));
            foreach ($parent_cate AS $key => $parent){
                foreach ($sub_cate AS $k => $sub){
                    if($sub['parent_id'] == $parent['id']){
                        $parent_cate[$key]['sub'][$k] = $sub;
                    }
                }
            }
            $title = '编辑职位';
            include $this->template('edit_company_job');
        }
    }


    /**
     * 删除招聘信息
     */
    public function doMobileDeleteAjax(){
        global $_W, $_GPC;
        if(pdo_delete($this->job_table, array('id' => $_GPC['jobid']))){
            exit('1');
        }else{
            exit('0');
        }
    }


	/**
	 * 我的企业信息
	 */
    public function doMobileMyCompanyInfo(){
    	global $_W, $_GPC;
        $config = $this->get_config();
    	if(checksubmit('savejobsubmit')){
    		if(false == $id = intval($_GPC['id'])){
    			message("传参错误", referer(), 'error');
    		}else {
                //接收提交数据
                $data = $_GPC['data'];
                //判断是否填充
                if(empty($data['name']) || empty($data['address']) || empty($data['contact']) || empty($data['mobile']) || empty($data['description']) ){
                    message("请填写完整", 'referer', 'error');
                }
                //是否开启运营执照上传
    			if ($this->module['config']['isopenlicense'] && !empty($_FILES['license']['name'])){
                    $data['license'] = $this->upload_img('license', 'license');
    			}

                //上传Logo
                if (!empty($_FILES['logo']['name'])) {
                    $data['logo'] = $this->upload_img('logo', 'logo', true, 160);   //首页160*120
                }

                //上传封面
                if (!empty($_FILES['avatar']['name'])) {
                    $data['avatar'] = $this->upload_img('avatar', 'avatar', true, 360);  //公司介绍页360*180
                }
    			if(pdo_update($this->company_table, $data, array('weid' => $this->weid, "id" => $id))){
    				message("成功保存", referer(), 'success');
    			}else {
    				message("没有修改或保存失败", referer(), 'error');
    			}
    		}
    	}else {
	    	//取企业注册信息
	    	$row = pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE weid = :weid AND from_user = :from_user", array(":weid" => $this->weid, ":from_user" => $this->from_user));
	    	//===============================取行业分类=========================
            $parent_industry = $this->get_all_industry();
	    	//是否开启营业执照上传
	    	$isopenlicense = $this->module['config']['isopenlicense'];
            load()->func('tpl');
            $title = '企业信息';
	    	include $this->template('my_company_info');
    	}
    }
	
	
    /**
     * 来应聘的
     */
    public function doMobileComeApply(){
    	global $_W, $_GPC;
    	$companyid = $_GPC['companyid'];
    	$applys = pdo_fetchall("SELECT * FROM ".tablename($this->apply_table)." WHERE weid = :weid AND company_id = :companyid", array(":weid" => $this->weid, ":companyid" => $companyid));
    	$tmp = $temp = array();
    	foreach ($applys AS $key => $apply){
    		array_unshift($tmp, $apply['person_id']);
    		array_unshift($temp, $apply['job_id']);
    		$applys[$key]['dateline'] = date("Y-m-d", $apply['dateline']);
    	}
    	//====用户=========
        if(!empty($tmp)){
            $persons_tmp = pdo_fetchall("SELECT `id`,`username` FROM ".tablename($this->person_table)." WHERE id IN (".implode(',', $tmp).")");
            $person = array();
            foreach ($persons_tmp AS $key => $val){
                $person[$val['id']] = $val;
            }
        }
    	//======职位===========
        if(!empty($temp)){
            $jobs_tmp = pdo_fetchall("SELECT `id`,`title` FROM ".tablename($this->job_table)." WHERE id IN (".implode(',', $temp).")");
            $jobs = array();
            foreach ($jobs_tmp AS $key => $val){
                $jobs[$val['id']] = $val;
            }
        }
        $title = '来应聘的';
    	include $this->template('my_company_comeapply');
    }


    /**
     * 公司地图
     */
    public function doMobileShowCompanyMap(){
        global $_W, $_GPC;
        $info = pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE id = :id", array(":id" => $_GPC['companyid']));
        $coordinate = json_decode($info['coordinate'], 1);
        $title = '查看地图';
        include $this->template('show_company_map');
    }


    //===========求职==============
    /**
     * 发布求职简历-基本信息
     */
    public function doMobilePublicResumeBasic(){
    	global $_W, $_GPC;
        //=========是否关注================================
        $oauth_openid="ThinkIdea_rencai_".$_W['uniacid'];
        if(empty($_COOKIE[$oauth_openid])){
            $this->getCode();
        }
        $this->getFollow();
        //================================================
    	$config = $this->get_config();
    	$time = time();
    	$person_id = $_GPC['person_id'];
    	
    	if(checksubmit('save_basic_submit')){
    		
    		//新录入
			if(empty($person_id)){
    			//写member表
    			$member_data = array(
    				'weid' => $this->weid,
    				'from_user' => $this->from_user,
    				'type' => 2
    			);
    			pdo_insert($this->member_table, $member_data);
    			$person_id = pdo_insertid();
    			
    			//写person表
    			$person_data = array(
    				'weid' => $this->weid,
    				'from_user' => $this->from_user,
    				'dateline' => $time,
    				'updatetime' => $time,
    			);
    			$person_data = array_merge($person_data, $_GPC['data']);


    			//=====================上传头像处理start==============
    			if (!empty($_FILES['uploadfile']['name'])){
    				$upfile = $_FILES['uploadfile'];
    				$name = $upfile['name'];
    				$type = $upfile['type'];
    				$size = $upfile['size'];
    				$tmp_name = $upfile['tmp_name'];
    				$error = $upfile['error'];
    				//上传路径
    				$upload_path = IA_ROOT."/attachment/thinkidea_rencai/avatar/";
    			        load()->func('file');@mkdirs($upload_path);
    				if(intval($error) > 0){
    					message('上传错误：错误代码：'.$error, 'referer', 'error');
    				}else {
    			
    					//上传文件大小0为不限制，默认2M
    					$maxfilesize = empty($this->module['config']['headimgurlsize']) ? 2 : intval($this->module['config']['headimgurlsize']);
    					if($maxfilesize > 0){
    						if($size > $maxfilesize * 1024 * 1024){
    							message('上传文件过大'.$_FILES["file"]["error"], 'referer', 'error');
    						}
    					}
    			
    					//允许上传的图片类型
    					$uptypes = array ('image/jpg','image/png','image/jpeg');
    					//判断文件的类型
    					if (!in_array($type, $uptypes)) {
    						message('上传文件类型不符：'.$type, 'referer', 'error');
    					}
    					//存放目录
    					if(!file_exists($upload_path)){
    						mkdir($upload_path);
    					}
    					//取文件后缀
    					//$suffix = strrev( substr(strrev($name), 0, strpos(strrev($name), '.')));
    					//移动文件
    					$source_filename = $person_id.'_'.date("Ymd");
    					$target_filename = $person_id.'_'.date("Ymd").'.thumb.jpg';
    			
    					if(!move_uploaded_file($tmp_name, $upload_path.$source_filename)){
    						message('移动文件失败', 'referer', 'error');
    					}
    					//营业执照进行缩略
    					$srcfile = $upload_path.$source_filename;
    					$desfile = $upload_path.$target_filename;
    					//文件操作类
    					load()->func('file');
    					$ret = file_image_thumb($srcfile, $desfile, 320);
    					//$ret = file_image_crop($srcfile, $desfile, 400, 400 ,5);//裁剪
    					if(!is_array($ret)){
    						//路径存入数据库
    						$person_data['headimgurl'] = $target_filename;
    					}
    					//删除原图
    					unlink($srcfile);
    				}
    			}
    			//=====================上传头像end==============    			
    			
    			if (pdo_insert($this->person_table, $person_data)){
    				message('添加成功', $this->createMobileUrl('My'), 'success');
    			}else {
    				message('操作失败或表单无变化', $this->createMobileUrl('My'), 'error');
    			}
    			
			}else {
				//更新时间
				$person_data['updatetime'] = $time;
				$person_data = array_merge($person_data, $_GPC['data']);
				
				if(pdo_update($this->person_table, $person_data, array('id' => $person_id))){
					message('保存成功', $this->createMobileUrl('My'), 'success');
				}else {
					message('操作失败或表单无变化#', $this->createMobileUrl('My'), 'error');
				}
			}
				
    	}else {
    		if(!empty($person_id)){
    			//判断是否已注册
    			$person = pdo_fetch("SELECT * FROM ".tablename($this->person_table)." WHERE id = :id LIMIT 1", array(":id" => $person_id));
    			//头像
    			$person['headimgurl'] = empty($person['headimgurl']) ? ($person['sex'] == 1 ? $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_man.jpg' : $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/default_woman.jpg' ) : $_W['attachurl'].'thinkidea_rencai/avatar/'.$person['headimgurl'];
    		}
            $title = '1：填写基本信息';
    		include $this->template('public_resume_basic');
    	}
    }

    
    /**
     * 发布求职简历-工作经验
     */
    public function doMobilePublicResumeWorkExperience(){
    	global $_W, $_GPC;
        //=========是否关注================================
        $oauth_openid="ThinkIdea_rencai_".$_W['uniacid'];
        if(empty($_COOKIE[$oauth_openid])){
            $this->getCode();
        }
        $this->getFollow();
        //================================================
    	$time = time();
    	$person_id = $_GPC['person_id'];
    	$resume_id = $_GPC['resume_id'];
    	
    	if (checksubmit('save_resume_workexperience')){
    		if(empty($resume_id)){
    			//写person_resume表
    			$resume_data = array(
    					'person_id' => $person_id,
    					'weid' => $this->weid,
    					'dateline' => $time,
    			);
    			$resume_data = array_merge($resume_data, $_GPC['data_resume']);
    	
    			if (pdo_insert($this->resume_table, $resume_data)){
    				message('添加成功', $this->createMobileUrl('PublicResumeWorkExperience'), 'success');
    			}else {
    				message('操作失败或表单无变化', $this->createMobileUrl('PublicResumeWorkExperience'), 'error');
    			}
    		}else {
    			if(pdo_update($this->resume_table, $_GPC['data_resume'], array('id' => $resume_id))){
    				message('保存成功', 'refresh', 'success');
    			}else {
    				message('操作失败或表单无变化', 'refresh', 'error');
    			}
    		}
    		
    	}else {
    		
    		//是否删除
    		if($_GPC['op'] == 'delete'){
    			if(pdo_delete($this->resume_table, array('id' => intval($_GPC['resume_id'])))){
    				message('删除成功', 'referer', 'success');
    			}else {
    				message('操作失败', 'referer', 'error');
    			}
    		}
    		
    		//简历列表
    		$resumes = pdo_fetchall("SELECT * FROM ".tablename($this->resume_table)." WHERE person_id = :person_id", array(":person_id" => $person_id));
    		foreach ($resumes AS $key => $resume){
    			$resumes[$key]['dateline'] = date("Y-m-d",$resume['dateline']);
    		}
    		
    			//单个简历
    		$resume_id = intval($_GPC['resume_id']);
    		if ($resume_id){
    			$op = 'edit';
    			$resume_info = pdo_fetch("SELECT * FROM ".tablename($this->resume_table)." WHERE id = :id LIMIT 1", array(":id" => $resume_id));
    		}
            $title = '2：填写工作经验';
    		include $this->template('public_resume_workexperience');
    	} 	
    	
    }

    
    /**
     * 查看简历
     */
    public function doMobileShowResumeInfo(){
    	global $_W, $_GPC;
        $config = $this->get_config();
    	
    	/**
    	 * 是否是企业用户
    	 * 是：更新查看简历数
    	 * 否：pass
    	 */
    	$company = pdo_fetch("SELECT `id`,`isauth`,`view_resume_nums`,`view_resume_total` FROM ".tablename($this->company_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
    	if(!empty($company)){
//    		if($company['isauth'] == 0){
//    			message('企业未认证，不可查看简历', $this->createMobileUrl("Resume"), 'error');
//    		}
    		if ($company['view_resume_nums'] >= $company['view_resume_total']){
    			message('查看简历数已用完，请购买', 'referer', 'error');
    		}
    		pdo_update($this->company_table, array('view_resume_nums' => intval($company['view_resume_nums'] + 1)), array('id' => $company['id']));
    	}
    	
    	$person_id = $_GPC['person_id'];
    	
    	//基本信息
    	$person = pdo_fetch("SELECT * FROM ".tablename($this->person_table)." WHERE id = :id LIMIT 1", array(":id" => $person_id));
    	$person['updatetime'] = date("Y-m-d",$person['updatetime']);
    	
    	//默认头像地址
    	$avatar_default_path = $_W['siteroot'].'addons/thinkidea_rencai/template/static/images/';
    	//头像判断
    	$person['headimgurl'] = empty($person['headimgurl']) ? ($person['sex'] == 1 ? $avatar_default_path.'default_man.jpg' : $avatar_default_path.'default_woman.jpg' ) : $_W['attachurl'].'thinkidea_rencai/avatar/'.$person['headimgurl'];
    	//简历列表
    	$resumes = pdo_fetchall("SELECT * FROM ".tablename($this->resume_table)." WHERE person_id = :person_id", array(":person_id" => $person_id));
    	if($resumes){
	    	foreach ($resumes AS $key => $resume){
	    		$resumes[$key]['dateline'] = date("Y-m-d",$resume['dateline']);
	    	}
    	}
    	//更新查看简历数
    	pdo_update($this->person_table, array('views' => intval($person['views']) + 1), array('id' => $person['id']));
        $title = '查看简历';
    	include $this->template('show_resume');
    }

    
    /**
     * 我的收藏
     */
    public function doMobileMyCollect(){
    	global $_W, $_GPC;
    	$person_id = intval($_GPC['person_id']);
    	
    	//所有收藏
    	$collects = pdo_fetchall("SELECT * FROM ".tablename($this->collect_table)." WHERE weid = :weid AND person_id = :person_id LIMIT 1", array(":weid" => $this->weid, ":person_id" => $person_id));
    	$jobs_id_tmp = $companys_id_tmp = $jobs = $companys = array();
    	foreach ($collects AS $key => $collect){
    		array_unshift($companys_id_tmp, $collect['company_id']); //去公司id
    		array_unshift($jobs_id_tmp, $collect['job_id']); //取职位id
    		$collects[$key]['dateline'] = date("Y/m/d", $collect['dateline']);
    	}
    	$collect_nums = count($collects); //收藏数

    	//职位名称
        if(!empty($jobs_id_tmp)){
            $jobs_tmp = pdo_fetchall("SELECT id,title FROM ".tablename($this->job_table)." WHERE id IN (".implode(',', $jobs_id_tmp).")");
            $jobs == array();
            foreach ($jobs_tmp AS $key => $val){
                $jobs[$val['id']] = $val;

            }
        }
    	//公司名称
        if(!empty($companys_id_tmp)){
            $companys_tmp = pdo_fetchall("SELECT id,name,isauth FROM ".tablename($this->company_table)." WHERE id IN (".implode(',', $companys_id_tmp).")");
            foreach ($companys_tmp AS $key => $val){
                $companys[$val['id']] = $val;
            }
        }
        $title = '我收藏的职位';
    	include $this->template('my_collect');
    }

    
    /**
     * 我的申请
     */
    public function doMobileMyApply(){
    	global $_W, $_GPC;
    	$person_id = intval($_GPC['person_id']);
    	
    	//所有收藏
    	$applys = pdo_fetchall("SELECT * FROM ".tablename($this->apply_table)." WHERE weid = :weid AND person_id = :person_id LIMIT 1", array(":weid" => $this->weid, ":person_id" => $person_id));
    	$jobs_id_tmp = $companys_id_tmp = $jobs = $companys = array();
    	foreach ($applys AS $key => $apply){
    		array_unshift($companys_id_tmp, $apply['company_id']); //去公司id
    		array_unshift($jobs_id_tmp, $apply['job_id']); //取职位id
    		$applys[$key]['dateline'] = date("Y/m/d", $apply['dateline']);
    	}
    	$applys_nums = count($applys); //收藏数
    	//职位名称
        if(!empty($jobs_id_tmp)){
            $jobs_tmp = pdo_fetchall("SELECT id,title FROM ".tablename($this->job_table)." WHERE id IN (".implode(',', $jobs_id_tmp).")");
            $jobs == array();
            foreach ($jobs_tmp AS $key => $val){
                $jobs[$val['id']] = $val;

            }
        }
    	//公司名称
        if(!empty($companys_id_tmp)){
            $companys_tmp = pdo_fetchall("SELECT id,name,isauth FROM ".tablename($this->company_table)." WHERE id IN (".implode(',', $companys_id_tmp).")");
            foreach ($companys_tmp AS $key => $val){
                $companys[$val['id']] = $val;
            }
        }
        $title = '我申请的职位';
    	include $this->template('my_apply');
    }


	/**
	 * 收藏职位Ajax
	 */    
    public function doMobileCollectJobAjax(){
    	global $_W, $_GPC;
    	
    	$member = pdo_fetch("SELECT * FROM ".tablename($this->member_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
    	if(empty($member)){
    		exit('-2');	//先检查有没有这个人
    	}
    	if($member['type'] == 1){
    		exit('-3'); //企业用户，不准收藏
    	}
    	
    	//用户id
    	$person_id = pdo_fetchcolumn("SELECT * FROM ".tablename($this->person_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user), 0);

    	//是否收藏
    	$collect = pdo_fetch("SELECT * FROM ".tablename($this->collect_table)." WHERE weid = :weid AND person_id = :person_id AND job_id = :job_id LIMIT 1", array(":weid" => $this->weid, ":person_id" => $person_id, ":job_id" => intval($_GPC['job_id'])));
    	if($collect){
    		exit('-1');
    	}
    	
    	$data = array(
    		'weid' => $this->weid,
    		'person_id' => $person_id,
    		'company_id' => intval($_GPC['company_id']),
    		'job_id' => intval($_GPC['job_id']),
    		'dateline' => time()
    	);
    	if(pdo_insert($this->collect_table, $data)){
    		exit('1');
    	}else {
    		exit('0');
    	}
    	
    } 
    
    
    /**
     * 申请职位Ajax
     */
    
    public function doMobileApplyJobAjax(){
    	global $_W, $_GPC;
    	
    	$member = pdo_fetch("SELECT * FROM ".tablename($this->member_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
    	if(empty($member)){
    		exit('-2');	//先检查有没有这个人
    	}
    	if($member['type'] == 1){
    		exit('-3'); //企业用户，不准收藏
    	}

    	//用户id
    	$person_id = pdo_fetchcolumn("SELECT * FROM ".tablename($this->person_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user), 0);
    	 
    	//是否申请
    	$apply = pdo_fetch("SELECT * FROM ".tablename($this->apply_table)." WHERE weid = :weid AND person_id = :person_id AND job_id = :job_id LIMIT 1", array(":weid" => $this->weid, ":person_id" => $person_id, ":job_id" => intval($_GPC['job_id'])));
    	if($apply){
    		exit('-1');
    	}
    	
    	$data = array(
    			'weid' => $this->weid,
    			'person_id' => $person_id,
    			'company_id' => intval($_GPC['company_id']),
    			'job_id' => intval($_GPC['job_id']),
    			'dateline' => time()
    	);
    	if(pdo_insert($this->apply_table, $data)){
    		exit('1');
    	}else {
    		exit('0');
    	}    	
    }

    /**
     * 注销身份
     */
    public function doMobileLogoutIdentity(){
        global $_W, $_GPC;
        if(pdo_update($this->member_table, array('status' => 0), array('weid' => $this->weid, 'from_user' => $this->from_user))){
            message('成功注销', $this->createMobileUrl('Index'), 'success');
        }else{
            message('操作失败', 'referer', 'error');
        }
    }


    //============================================兼职======================================
    /**
     * 发布兼职
     */
    public function doMobilePublicRartTime(){
        global $_W, $_GPC;
        $title = '发布兼职';
        include $this->template('public_parttime');
    }


    //===========================================WEB后台管理=================================
    /**
     * 招聘企业管理
     */
	public function doWebZhaounit() {
		global $_W, $_GPC;
        $config = $this->get_config();
		
		$lists = pdo_fetchall("SELECT * FROM ".tablename($this->company_table)." WHERE weid = :weid", array(":weid" => $this->weid));
		foreach ($lists AS $key => $val){
			$lists[$key]['type'] = $config['companytype'][$val['type']];
		}
		
		//所有行业分类
		$categorys = pdo_fetchall("SELECT * FROM ".tablename($this->industry_table)." WHERE weid = :weid", array(":weid" => $this->weid));
		$tmp = array();
		foreach ($categorys AS $key => $cate){
			$tmp[$cate['id']] = $cate;
		}
		foreach ($lists AS $key => $val){
			$lists[$key]['cname'] = $tmp[$val['industry']]['name'];
		}
		include $this->template('zhao_unit');
	}


    /**
     * 招聘企业编辑
     */
    public function doWebZhaounitEdit(){
        global $_W, $_GPC;
        if (checksubmit('save_info')) {
            $id = intval($_GPC['companyid']);
            $_GPC['data']['coordinate'] = json_encode($_GPC['data']['coordinate']);
            if(pdo_update($this->company_table, $_GPC['data'], array('id' => $id))){
                message("保存成功", $this->createWebUrl('Zhaounit'), 'success');
            }else{
                message("保存失败", $this->createWebUrl('Zhaounit'), 'error');
            }

        }else{
            $config = $this->get_config();
            $id = intval($_GPC['id']);
            $row = pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE  id = :id AND weid = :weid LIMIT 1", array(":id" => $id, ":weid" => $this->weid));
            $coordinate = json_decode($row['coordinate'], 1);

            //行业分类=取父类
            $parents = pdo_fetchall("SELECT * FROM ".tablename($this->industry_table)." WHERE weid = :weid AND parent_id = 0 ORDER BY display ASC", array(":weid" => $this->weid));
            $tmp = array();
            foreach ($parents AS $parent){
                array_push($tmp, $parent['id']);
            }
            $pids = implode(",", $tmp);
            unset($tmp);
            if(!empty($pids)){
                //取子类
                $subs = pdo_fetchall("SELECT * FROM ".tablename($this->industry_table)." WHERE weid = :weid AND parent_id IN ({$pids}) ORDER BY display ASC", array(":weid" => $this->weid));
                foreach ($parents AS $key => $parent){
                    foreach ($subs AS  $k => $sub){
                        if($sub['parent_id'] == $parent['id']){
                            $parents[$key]['sub'][$k] = $sub;
                        }
                    }
                }
            }
            load()->func('tpl');
            include $this->template('zhao_unit_edit');
        }
    }


	/**
	 * 后台审核企业状态AJAX
	 */
	public function doWebAuditCompanyStatusAjax(){
		global $_W, $_GPC;
		$company_id = $_GPC['company_id'];
		$status = $_GPC['change_to'];
		
		$data = array('status' => intval($status));
		$filter  = array('id' => intval($company_id), 'weid' => $this->weid);
		if(false !== pdo_update($this->company_table, $data, $filter)){
			exit('1');
		}else{
			exit('0');
		}
	} 
	
	
	/**
	 * 删除企业Ajax
	 */
	public function doWebDeleteCompanyAjax(){
		global $_W, $_GPC;
		$company_id = intval( $_GPC['company_id']);
        $info = pdo_fetch("SELECT from_user FROM ".tablename($this->company_table)." WHERE id = :id LIMIT 1", array(":id" => $company_id));
        pdo_delete($this->member_table, array('weid' => $this->weid, 'from_user' => $info['from_user']));
        pdo_delete($this->company_table, array('id' => $company_id));

	}
	
	
	/**
	 * 后台企业认证状态AJAX
	 */
	public function doWebAuditCompanyAuthAjax(){
		global $_W, $_GPC;
		$company_id = $_GPC['company_id'];
		$status = $_GPC['change_to'];
	
		$data = array('isauth' => intval($status));
		$filter  = array('id' => intval($company_id));
		if(false !== pdo_update($this->company_table, $data, $filter)){
			exit('1');
		}else{
			exit('0');
		}
	}
	
	
	/**
	 * 企业可查看简历数
	 */
	public function doWebAuditViewResumeTotal(){
		global $_W, $_GPC;
		$company_id = $_GPC['company_id'];
		
		if (checksubmit('save_info')) {
			$view_resume_total = $_GPC['view_resume_total'];
			$company_id =  $_GPC['company_id'];
			
			if(false !== pdo_update($this->company_table, array('view_resume_total' => $view_resume_total), array('id' => $company_id))){
				message("保存成功", $this->createWebUrl('Zhaounit'), 'success');
			}
		}else {
			//取当前可查看简历数
			$row = pdo_fetch("SELECT `view_resume_total` FROM ".tablename($this->company_table)." WHERE id = :id ", array(":id" => $company_id));
			include $this->template('audit_viewresumetotal');
		}	
		
	}
	
	/**
	 * 后台职位信息置顶AJAX
	 */
	public function doWebAuditTopInfoStatusAjax(){
		global $_W, $_GPC;
		$info_id = $_GPC['info_id'];
		$status = $_GPC['change_to'];
	
		$data = array('istop' => intval($status), 'expiration' => 0);
		$filter  = array('id' => intval($info_id));
		if(false !== pdo_update($this->job_table, $data, $filter)){
			exit('1');
		}else{
			exit('0');
		}
	}


    /**
     * 后台职位信息热门AJAX
     */
    public function doWebAuditHotInfoStatusAjax(){
        global $_W, $_GPC;
        $info_id = $_GPC['info_id'];
        $status = $_GPC['change_to'];

        $data = array('ishot' => intval($status));
        $filter  = array('id' => intval($info_id));
        if(false !== pdo_update($this->job_table, $data, $filter)){
            exit('1');
        }else{
            exit('0');
        }
    }

	
	/**
	 * 后台信息置顶设置
	 */
	public function doWebAuditTopInfo(){
		global $_GPC,$_W;
        $config = $this->get_config();
		if (checksubmit('save_topinfo')) {
			$data = array('istop' => 1, 'expiration' => strtotime($_GPC['expiration']));
            $filter = array('id' => intval($_GPC['info_id']));
			if(false !== pdo_update($this->job_table, $data, $filter)){
				message("有效期设置成功", $this->createWebUrl('Zhaoinfo'), 'success');
			}
		}else {
			$info_id = intval($_GPC['info_id']);
            load()->func('tpl');
			include $this->template('top_info');
		}	
	}
	
	/**
	 * 招聘职位管理
	 */
	public function doWebZhaoinfo() {
		global $_W, $_GPC;
        $config = $this->get_config();
		//所有职位
		$lists = pdo_fetchall("SELECT * FROM ".tablename($this->job_table)." WHERE weid = :weid", array(":weid" => $this->weid));
		//所有职位分类
		$categorys = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND isshow = 1", array(":weid" => $this->weid));
		$tmp = array();
		foreach ($categorys AS $key => $cate){
			$tmp[$cate['id']] = $cate;
		}
		foreach ($lists AS $key => $val){
			$lists[$key]['cname'] = $tmp[$val['cid']]['name'];
		}
		
		include $this->template('zhao_info');
	}


    /**
     * 职位编辑
     */
    public function doWebZhaoinfoEdit(){
        global $_W, $_GPC;
        $config = $this->get_config();
        $jobid = intval($_GPC['jobid']);
        if (checksubmit('save_info')) {
            $data = $_GPC['data'];
            $data['welfare'] = implode(',', $_GPC['welfare']);
            if(pdo_update($this->job_table, $_GPC['data'], array('id' => $jobid))){
                message('保存成功', $this->createWebUrl('Zhaoinfo'), 'success');
            }else{
                message('操作失败或无改动', $this->createWebUrl('Zhaoinfo'), 'error');
            }
        }else{
            $row =  pdo_fetch("SELECT * FROM ".tablename($this->job_table)." WHERE id = :id LIMIT 1", array(":id" => $jobid));
            $welfare_array = explode(',', $row['welfare']);
            $offices = $this->get_all_office();
            load()->func('tpl');
            include $this->template('zhao_info_edit');
        }

    }


    /**
	 * 招聘职位删除管理
	 */
	public function doWebZhaoinfoDeleteAjax(){
		global $_W, $_GPC;
		$id = intval( $_GPC['info_id'] );
		if(pdo_delete($this->job_table, array('id' => $id))){
			exit('1');
		}else{
			exit('0');
		}
	}
	

	/**
	 * 设置各个页面的分享
	 */
	public function doWebSetShareInfo(){
		global $_GPC,$_W;
		if (checksubmit('save_shareinfo_submit')) {
			$shareid = intval($_GPC['shareid']);
			$data = $_GPC['data'];
			if($shareid){
				if(pdo_update($this->share_table, $data, array('id' => $shareid))){
					message('更新成功',referer(),'success');
				}else {
					message('更新失败，联系作者',referer(),'error');
				}
			}else {
				if(pdo_insert($this->share_table, $data)){
					message('添加成功',referer(),'success');
				}else {
					message('添加失败，联系作者',referer(),'error');
				}
			}
			
		} else {
			$share = pdo_fetch("SELECT * FROM ".tablename($this->share_table));
			load()->func('tpl');
			include $this->template('shareinfo');
		}
	}


	/**
	 * 职位分类
	 */
	public function doWebCategory(){
		global $_GPC,$_W;
		
		if (checksubmit('save_category')) {
			$data = $_GPC['data'];
			$data['weid'] = $this->weid;
			
			if(isset($_GPC['cid']) && !empty($_GPC['cid'])){
				$cid = intval($_GPC['cid']);
				if(pdo_update($this->category_table, $data, array('id' => $cid))){
					message('操作成功',$this->createWebUrl('Category'),'success');
				}else {
					message('操作失败#', $this->createWebUrl('Category'),'error');
				}
			}else {
				if(pdo_insert($this->category_table, $data)){
					message('操作成功',$this->createWebUrl('Category'),'success');
				}else {
					message('操作失败~', $this->createWebUrl('Category'),'error');
				}
			}
		}else {
			$op = isset($_GPC['op']) ? $_GPC['op'] : 'display';
				
			//取父类
			$parents = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id = 0 ORDER BY display ASC", array(":weid" => $this->weid));
			$tmp = array();
			foreach ($parents AS $parent){
				array_push($tmp, $parent['id']);
			}
			$pids = implode(",", $tmp);
			unset($tmp);
			if(!empty($pids)){	
				//取子类
				$subs = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id IN ({$pids}) ORDER BY display ASC", array(":weid" => $this->weid));
				foreach ($parents AS $key => $parent){
					foreach ($subs AS  $k => $sub){
						if($sub['parent_id'] == $parent['id']){
							$parents[$key]['sub'][$k] = $sub;
						}
					}
				}
			}
			//行业分类
			$row = pdo_fetch("SELECT * FROM ".tablename($this->category_table)." WHERE id = :id AND weid = :weid", array(":id" => intval($_GPC['id']), ":weid" => $this->weid));
				
			include $this->template('category');
		}
		
	}		
	
	
	/**
	 * 删除职位分类AJAX
	 */
	public function doWebDeleteCategoryAjax(){
		global $_GPC,$_W;
		//检查是否是父栏目
		$ret = pdo_fetch("SELECT * FROM ".tablename($this->category_table)." WHERE parent_id = :parent_id LIMIT 1", array(":parent_id" => intval($_GPC['cid'])));
		if($ret !== FALSE){
			exit('-2');
		}else {
			if(false !== pdo_query("DELETE FROM ".tablename($this->category_table)." WHERE id = :id LIMIT 1", array(":id" => intval($_GPC['cid'])))){
				exit('删除成功');
			}else{
				exit('操作失败');
			}
		}
	}


	/**
	 * 行业分类
	 * 关于每个weid下的行业分类id，大家都用一个ID。不作区分了。2015.2.14
	 */
	public function doWebIndustry(){
		global $_GPC,$_W;
	
		if (checksubmit('save_industry')) {
			$data = $_GPC['data'];
			$data['weid'] = $this->weid;
			$data['dateline'] = time();
			
			if(isset($_GPC['cid']) && !empty($_GPC['cid'])){
				$cid = intval($_GPC['cid']);
				if(pdo_update($this->industry_table, $data, array('id' => $cid))){
					message('操作成功',$this->createWebUrl('Industry'),'success');
				}else {
					message('操作失败', $this->createWebUrl('Industry'),'error');
				}
			}else {
				if(pdo_insert($this->industry_table, $data)){
					message('操作成功',$this->createWebUrl('Industry'),'success');
				}else {
					message('操作失败', $this->createWebUrl('Industry'),'error');
				}	
			}
		}else {
			$op = isset($_GPC['op']) ? $_GPC['op'] : 'display';
			
			//取父类
			$parents = pdo_fetchall("SELECT * FROM ".tablename($this->industry_table)." WHERE weid = :weid AND parent_id = 0 ORDER BY display ASC", array(":weid" => $this->weid));
			$tmp = array();
			foreach ($parents AS $parent){
				array_push($tmp, $parent['id']);
			}
			$pids = implode(",", $tmp);
			unset($tmp);
			if(!empty($pids)){
				//取子类
				$subs = pdo_fetchall("SELECT * FROM ".tablename($this->industry_table)." WHERE weid = :weid AND parent_id IN ({$pids}) ORDER BY display ASC", array(":weid" => $this->weid));
				foreach ($parents AS $key => $parent){
					foreach ($subs AS  $k => $sub){
						if($sub['parent_id'] == $parent['id']){
							$parents[$key]['sub'][$k] = $sub;
	 					}
					}
				}
			}
			//行业分类
			$row = pdo_fetch("SELECT * FROM ".tablename($this->industry_table)." WHERE id = :id AND weid = :weid", array(":id" => intval($_GPC['id']), ":weid" => $this->weid));
			
			include $this->template('industry');
		}
		
	}	
	
	
	/**
	 * 删除行业分类AJAX
	 */
	public function doWebDeleteIndustryAjax(){
		global $_GPC,$_W;
		//检查是否是父栏目
		$ret = pdo_fetch("SELECT * FROM ".tablename($this->industry_table)." WHERE parent_id = :parent_id LIMIT 1", array(":parent_id" => intval($_GPC['cid'])));
		if($ret !== FALSE){
			exit('-2');
		}else {
			if(false !== pdo_query("DELETE FROM ".tablename($this->industry_table)." WHERE id = :id LIMIT 1", array(":id" => intval($_GPC['cid'])))){
				exit('删除成功');
			}else{
				exit('操作失败');
			}
		}
	}

	
	/**
	 * 简历管理
	 */
	public function doWebResume(){
		global $_GPC,$_W;
        $config = $this->get_config();

		$persons = pdo_fetchall("SELECT * FROM ".tablename($this->person_table)." WHERE weid = :weid", array(":weid" => $this->weid));
		foreach ($persons AS $key => $val){
		}	

		include $this->template('resume');
	}
	
	
	/**
	 * 简历编辑修改
	 */
	public function doWebResumeEdit(){
		global $_W, $_GPC;
        $config = $this->get_config();
        $resumeid = intval($_GPC['resumeid']);
        if (checksubmit('save_info')) {
            if(pdo_update($this->person_table, $_GPC['data'], array('id' => $resumeid))){
                message('保存成功', $this->createWebUrl('Resume'), 'success');
            }else{
                message('操作失败或无改动', $this->createWebUrl('Resume'), 'error');
            }
        }else{
            $row = pdo_fetch("SELECT * FROM ".tablename($this->person_table)." WHERE id = :id LIMIT 1",array(":id" => $resumeid));
            load()->func('tpl');
            include $this->template('resume_edit');
        }

	}


    /**
     * 简历删除
     */
    public function doWebResumeDeleteAjax(){
        global $_W, $_GPC;
        $personid = $_GPC['personid'];
        //是否填写工作经验的
        $ret = pdo_fetch("SELECT * FROM ".tablename($this->resume_table)." WHERE person_id = :personid LIMIT 1", array(':personid' => $personid));
        if($ret){
            pdo_delete($this->resume_table, array('person_id' => $personid));
        }
        pdo_delete($this->person_table, array('id' =>$personid));
    }

	/**
	 * 后台职位信息置顶AJAX
	 */
	public function doWebAuditResumeTopInfoAjax(){
		global $_W, $_GPC;
		$person_id = $_GPC['person_id'];
		$status = $_GPC['change_to'];
	
		$data = array('istop' => intval($status), 'expiration' => 0);
		$filter  = array('id' => intval($person_id));
		if(false !== pdo_update($this->person_table, $data, $filter)){
			exit('1');
		}else{
			exit('0');
		}
	}

	
	/**
	 * 后台简历置顶设置
	 */
	public function doWebAuditResumeTopInfo(){
		global $_GPC,$_W;
        $config = $this->get_config();
		
		if (checksubmit('save_topinfo')) {
			$validity = intval($_GPC['validity']);
			$data = array('istop' => 1, 'expiration' => strtotime(" +".$validity." days"));
			$filter = array('id' => intval($_GPC['person_id']));
			if(false !== pdo_update($this->person_table, $data, $filter)){
				message("有效期设置成功", $this->createWebUrl('Resume'), 'success');
			}
		}else {
			$person_id = intval($_GPC['person_id']);
			include $this->template('resume_top_info');
		}	
	}
	
	
	/**
	 * 使用必读
	 */
	public function doWebReadme(){
		include $this->template('readme');
	}
	
	
	/**
	 * 广告投放管理(幻灯片)
	 */
	public function doWebADSlider(){
		global $_GPC,$_W;
		$op = empty($_GPC['op']) ? 'display' : $_GPC['op'];
		if (checksubmit('submit')) {
			$insert_data = array(
				'weid' => $this->weid,
				'dateline' => time(),
			);
            $time = $_GPC['data']['exprtime'];
            $_GPC['data']['exprtime'] = strtotime($time);

			$insert_data = array_merge($insert_data, $_GPC['data']);
			if(empty($_GPC['id'])){
				if(false !== pdo_insert($this->ads_table, $insert_data)){
					message("添加成功", $this->createWebUrl('ADSlider'), 'success');
				}else{
					message("操作失败", 'referer', 'error');
				}		
			}else {
				if(false !== pdo_update($this->ads_table, $insert_data, array('id' => intval($_GPC['id'])))){
					message("更新成功", $this->createWebUrl('ADSlider'), 'success');
				}else {
					message("更新失败", 'referer', 'error');
				}
			}	
		}else{
			if($op == 'display'){
				$lists = pdo_fetchall("SELECT * FROM ".tablename($this->ads_table)." WHERE weid = :weid", array(":weid" => $this->weid));
			}else {
				$row = pdo_fetch("SELECT * FROM ".tablename($this->ads_table)." WHERE id = :id LIMIT 1", array(":id" =>  intval($_GPC['id'])));
            }
			load()->func('tpl');
			include $this->template('adslider');
		}
	}


    /**
     * 删除幻灯
     */
    public function doWebADSliderDeleteAjax(){
        global $_GPC,$_W;
        if(pdo_delete($this->ads_table, array("id" => intval($_GPC['id'])))){
            exit('删除成功');
        }else{
            exit('删除失败');
        }
    }


    /**
     * 分享设置
     */
    public function doWebShare(){
        global $_GPC,$_W;
        if(checksubmit('save_shareinfo_submit')){
            $shareid = $_GPC['shareid'];
            $data = $_GPC['data'];
            if(empty($shareid)){
                $data = array_merge($data, array('uniacid' => $this->weid));
                if(pdo_insert($this->share_table, $data)){
                    message("添加成功", 'referer', 'success');
                }else{
                    message("添加失败", 'referer', 'error');
                }
            }else{
                if(pdo_update($this->share_table, $data, array('id' => $shareid))){
                    message("更新成功", 'referer', 'success');
                }else{
                    message("更新失败", 'referer', 'error');
                }
            }
        }else{
            $share = pdo_fetch("SELECT * FROM ".tablename($this->share_table)." WHERE uniacid = :uniacid LIMIT 1", array(':uniacid' => $this->weid));
            load()->func('tpl');
            include $this->template('share');
        }
    }


	//========================================OAuth2.0验证=============================
	
	public function get_curl($url){
		$ch=curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$data  =  curl_exec($ch);
		curl_close($ch);
		return json_decode($data,1);
	}


	public function post_curl($url,$post=''){
		$ch=curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		$data  =  curl_exec($ch);
		curl_close($ch);
		return json_decode($data,1);
	}
	

	private function getCode(){
		global $_GPC,$_W;
		$appid = $_W['account']['key'];
		$secret = $_W['account']['secret'];
		$level = $_W['account']['level'];
		
		if($level == 4){
			$oauth_openid="ThinkIdea_rencai_".$_W['uniacid'];
			if(empty($_COOKIE[$oauth_openid])){
				$redirect_uri = url('entry&do=GetToken&m=thinkidea_rencai', '', true);
				$redirect_uri = $_W['siteroot'].'app/'.$redirect_uri;
				$url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appid.'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=snsapi_base&state=0#wechat_redirewct';
				header('Location: '.$url, true, 301);
			}
		}else{
			return '';
		}
	}

	/**
	 * 取token，返回openid
	 */
	public function doMobileGetToken(){
		global $_GPC,$_W;
		$appid = $_W['account']['key'];
		$secret = $_W['account']['secret'];
		$url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$_GPC['code'].'&grant_type=authorization_code';
		$data = $this->get_curl($url); //windows 部分服务器curl不管用
		if(empty($data)){
			$data =	file_get_contents($url);
			$data = json_decode($data, 1);
		}
		//写cookie
		$oauth_openid="ThinkIdea_rencai_".$_W['uniacid'];
		setcookie($oauth_openid, $data['openid'], time() + self::$COOKIE_DAYS * 24 * 60 * 60 );
		
		//跳回		
		header('Location:'.$this->createMobileUrl('index'), true, 301);
	}


	/**
	 * //判断是否关注
	 * @return boolean
	 */
	public function getFollow(){
		global $_GPC,$_W;
		$p = pdo_fetch("SELECT follow FROM ".tablename('mc_mapping_fans')." WHERE uniacid = :weid AND openid = :openid LIMIT 1", array(":weid" => $this->weid, ":openid" => $this->from_user));
		if(intval($p['follow']) == 0){
			header('Location: '.$this->createMobileUrl('FansUs'), true, 301);
		}else{
			return true;
		}		
	}


    //=========================================Tools=====================================
    /**
     * @return mixed
     * 当前用户类型
     */
    public function get_memner_type(){
        global $_GPC,$_W;
        return pdo_fetchcolumn("SELECT `type` FROM ".tablename($this->member_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user), 0);
    }


    /**
     * @return mixed
     * 取当前登录用户id
     */
    public function get_member_id(){
        global $_GPC,$_W;
        $type = $this->get_memner_type();
        if($type == 1){ //企业
            return pdo_fetchcolumn("SELECT `id` FROM ".tablename($this->company_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user), 0);
        }else{
            return pdo_fetchcolumn("SELECT `id` FROM ".tablename($this->person_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user), 0);
        }
    }


    /**
     * @return mixed
     * 取用户信息
     */
    public function get_member_info(){
        global $_GPC,$_W;
        $type = $this->get_memner_type();
        if($type == 1){ //企业
            return pdo_fetch("SELECT * FROM ".tablename($this->company_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
        }else{
            return pdo_fetch("SELECT * FROM ".tablename($this->person_table)." WHERE weid = :weid AND from_user = :from_user LIMIT 1", array(":weid" => $this->weid, ":from_user" => $this->from_user));
        }
    }


    /**
     * @param $uid
     * @param $jobid
     * @return bool
     * 是否申请该职位
     */
    public function get_is_apply($uid, $jobid){
        global $_GPC,$_W;
        $ret = pdo_fetch("SELECT id FROM ".tablename($this->apply_table)." WHERE weid = :weid AND person_id = :person_id AND job_id = :job_id LIMIT 1", array(':weid' => $this->weid, ':person_id' => $uid, ':job_id' => $jobid));
        if($ret){
            return true;
        }else{
            return false;
        }
    }


    /**
     * @param $uid
     * @param $jobid
     * @return bool
     * 是否收藏该职位
     */
    public function get_is_collect($uid, $jobid){
        global $_GPC,$_W;
        $ret = pdo_fetch("SELECT id FROM ".tablename($this->collect_table)." WHERE weid = :weid AND person_id = :person_id AND job_id = :job_id LIMIT 1", array(':weid' => $this->weid, ':person_id' => $uid, ':job_id' => $jobid));
        if($ret){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @return mixed
     * 返回所有职位分类
     */
    public function get_all_office(){
        global $_GPC,$_W;
        $parent_cate = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id = 0 AND isshow = 1 ORDER BY display ASC", array(":weid" => $this->weid));
        $tmp = array();
        foreach ($parent_cate AS $parent){
            array_push($tmp, $parent['id']);
        }
        $tmp = implode(",", $tmp);
        $sub_cate = pdo_fetchall("SELECT * FROM ".tablename($this->category_table)." WHERE weid = :weid AND parent_id IN (". $tmp .") AND isshow = 1 ORDER BY display ASC", array(":weid" => $this->weid));

        foreach ($parent_cate AS $key => $parent){
            foreach ($sub_cate AS $k => $sub){
                if($sub['parent_id'] == $parent['id']){
                    $parent_cate[$key]['sub'][$k] = $sub;
                }
            }
        }
        return $parent_cate;
    }


    /**
     * 所有行业分类
     * @return mixed
     */
    public function get_all_industry(){
        global $_GPC,$_W;
        $parent_industry = pdo_fetchall("SELECT * FROM ".tablename($this->industry_table)." WHERE weid = :weid AND parent_id = 0 AND isshow = 1 ORDER BY display ASC", array(":weid" => $this->weid));
        $tmp = array();
        foreach ($parent_industry AS $parent){
            array_push($tmp, $parent['id']);
        }
        $pids = implode(",", $tmp);
        unset($tmp);
        if(!empty($pids)){
            //取子类
            $sub_industry = pdo_fetchall("SELECT * FROM ".tablename($this->industry_table)." WHERE weid = :weid AND parent_id IN ({$pids}) AND isshow = 1 ORDER BY display ASC", array(":weid" => $this->weid));
            foreach ($parent_industry AS $key => $parent){
                foreach ($sub_industry AS  $k => $sub){
                    if($sub['parent_id'] == $parent['id']){
                        $parent_industry[$key]['sub'][$k] = $sub;
                    }
                }
            }
        }
        return $parent_industry;
    }
    /**
     * @param $ids
     * @return mixed
     * 根据企业id取企业id、名称
     */
    public function get_companys_info($ids){
        global $_GPC,$_W;
        $tmp_companys = pdo_fetchall("SELECT id,name,isauth FROM ".tablename($this->company_table)." WHERE id IN (".implode(',', $ids).")");
        $companys = array();
        foreach ($tmp_companys AS $company){
            $companys[$company['id']] = $company;
        }
        return $companys;
    }


    /**
     * @param $ids
     * @return array
     * 根据用户id取用户id、姓名、性别、头像
     */
    public function get_person_info($ids){
        global $_GPC,$_W;
        $tmp_persons = pdo_fetchall("SELECT id,username,sex,headimgurl FROM ".tablename($this->person_table)." WHERE id IN (".implode(',', $ids).")");
        $persons = array();
        foreach ($tmp_persons AS $person){
            $persons[$person['id']] = $person;
        }
        return $persons;
    }


    /**
     * @param $upload_name
     * @param string $asname
     * @param bool $thumb
     * @param int $width
     * @param int $height
     * @param int $position
     * @return string
     */
    public function upload_img($upload_name, $asname = '', $thumb = true, $width = 320, $height = 240, $position = 5){
        //文件操作类
        load()->func('file');
        $upfile = $_FILES[$upload_name];
        $name = $upfile['name'];
        $type = $upfile['type'];
        $size = $upfile['size'];
        $tmp_name = $upfile['tmp_name'];
        $error = $upfile['error'];
        //上传路径
        $upload_path = IA_ROOT."/attachment/thinkidea_rencai/";
        load()->func('file');@mkdirs($upload_path);

        if(intval($error) > 0){
            message('上传错误：错误代码：'.$upload_name.'-'.$error, 'referer', 'error');
        }else {

            //上传文件大小0为不限制，默认2M
            $maxfilesize = empty($this->module['config']['maxfilesize']) ? 2 : intval($this->module['config']['maxfilesize']);
            if($maxfilesize > 0){
                if($size > $maxfilesize * 1024 * 1024){
                    message('上传文件过大'.$_FILES["file"]["error"], 'referer', 'error');
                }
            }

            //允许上传的图片类型
            $uptypes = array ('image/jpg','image/png','image/jpeg');
            //判断文件的类型
            if (!in_array($type, $uptypes)) {
                message('上传文件类型不符：'.$type, 'referer', 'error');
            }
            //存放目录
            if(!file_exists($upload_path)){
                mkdir($upload_path);
            }
            //移动文件
            if(!move_uploaded_file($tmp_name, $upload_path.date("YmdHi").'_'.$name)){
                message('移动文件失败，请检查服务器权限', 'referer', 'error');
            }

            $srcfile = $upload_path.date("YmdHi").'_'.$name;
            $desfile = $upload_path.date("YmdHi").'_'.$name.'.'.$asname.'.thumb.jpg';
            if($thumb){
                file_image_thumb($srcfile, $desfile, $width);
            }else{
                file_image_crop($srcfile, $desfile, $width, $height, 5);
            }
            return date("YmdHi").'_'.$name.'.'.$asname.'.thumb.jpg';
        }
    }


    /**
     * 薪资
     * @return array
     */
    public function get_config(){
        $config = array();
        //薪资
        $payrolls = $this->module['config']['payroll'];
        $payrolls = explode("\n", $payrolls);
        foreach($payrolls AS $key => $val){
            $payrolls[$key+1] = $val;
        }
        unset($payrolls[0]);
        $config['payroll'] = $payrolls;

        //福利
        $welfares = $this->module['config']['welfare'];
        $welfares = explode("\n", $welfares);
        foreach($welfares AS $key => $val){
            $welfares[$key+1] = $val;
        }
        unset($welfares[0]);
        $config['welfare'] = $welfares;

        //学历
        $educationals = $this->module['config']['educational'];
        $config['educational'] = explode("\n", $educationals);

        //职位类型
        $positiontype = $this->module['config']['positiontype'];
        $positiontype = explode("\n", $positiontype);
        foreach($positiontype AS $key => $val){
            $positiontype[$key+1] = $val;
        }
        unset($positiontype[0]);
        $config['positiontype'] = $positiontype;

        //工作经验
        $workexperience = $this->module['config']['workexperience'];
        $config['workexperience']= explode("\n", $workexperience);

        //公司性质
        $companytype = $this->module['config']['companytype'];
        $config['companytype'] = explode("\n", $companytype);

        //公司规模
        $scale = $this->module['config']['scale'];
        $config['scale'] = explode("\n", $scale);

        return $config;
    }

}





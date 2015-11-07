<?php

/**
 * 一战到底模块处理程序
 *
 * @author  ZOMBIESZY QQ:214983937
 */
defined('IN_IA') or exit ('Access Denied');

class Zombie_fightingModuleProcessor extends WeModuleProcessor {
	
	public function respond() {
		global $_W;
		$rid = $this->rule;
		$from_user = $this->message['from'];
		if($rid) {
			$row = pdo_fetch("SELECT * FROM " . tablename('fighting_setting') . " WHERE rid = :rid", array(':rid' => $rid));

            if($row) {
                if (time() < $row['start']) {//未开始
                    return $this->respText($row['title'].'活动还未开始，请关注其他活动吧。');
                } elseif ((time() > $row['end']) || ($row['status'] == 2)) {//活动已结束时回复语
                    return $this->respText($row['title'].'活动活动已结束，请关注其他活动吧。');
                } elseif ($row['status'] == 1) {//暂停
                    return $this->respText($row['title'].'活动活动已暂停，请关注其他活动吧。');
                }else{
                    return $this->respNews(array(
                        'title' => $row['title'] == '' ? '一站到底': $row['title'],
                        'description' => strip_tags($row['description']) == '' ? '一站到底': strip_tags($row['description']),
                        'picUrl' => $_W['attachurl'] . $row['picture'],
                        'url' => $this->createMobileUrl('index', array('id' => $rid,'openid'=>$from_user),true),
                    ));
                }
            }

		}
		return null;
	}
	

	private function requireQuestions() {
		$sql_question = "SELECT * FROM `ims_fighting_question_bank` WHERE id >= (SELECT floor( RAND() * ((SELECT MAX(id) FROM `ims_fighting_question_bank`)-(SELECT MIN(id) FROM `ims_fighting_question_bank`)) + (SELECT MIN(id) FROM `ims_fighting_question_bank`))) ORDER BY id LIMIT 1";
		$question = pdo_fetch($sql_question);
		//print_r($question);
		switch ($question[option_num]) {
			case '2' :
				$str = "问题：\n{$question[question]}\n选项：\nA、{$question[optionA]} B、{$question[optionB]}";
				break;
			case '3' :
				$str = "问题：\n{$question[question]}\n选项：\nA、{$question[optionA]} B、{$question[optionB]} C、{$question[optionC]}";
				break;
			case '4' :
				$str = "问题：\n{$question[question]}\n选项：\nA、{$question[optionA]} B、{$question[optionB]} C、{$question[optionC]} D、{$question[optionD]}";
				break;
			case '5' :
				$str = "问题：\n{$question[question]}\n选项：\nA、{$question[optionA]} B、{$question[optionB]} C、{$question[optionC]} D、{$question[optionD]} E、{$question[optionE]}";
				break;
			case '6' :
				$str = "问题：\n{$question[question]}\n选项：\nA、{$question[optionA]} B、{$question[optionB]} C、{$question[optionC]} D、{$question[optionD]} E、{$question[optionE]} F、{$question[optionF]}";
				break;
		}
		//获取答案
		$an_arr = str_split($question[answer]);
		for ($i = 0; $i < $question[option_num]; $i++) {
			if ($an_arr[$i] == 1) {
				switch ($i) {
					case '0' :
						$an .= A;
						break;
					case '1' :
						$an .= B;
						break;
					case '2' :
						$an .= C;
						break;
					case '3' :
						$an .= D;
						break;
					case '4' :
						$an .= E;
						break;
					case '5' :
						$an .= F;
						break;
				}
			}
		}
		$result['question'] = "分值为【" . $question['figure'] . "】分的" . $str . "\n—————————-——\n回答请输入 X 如 A 或 ACD ！离开请输入 退出 题目原始id为<" . $question[id] . ">如有错误请反馈给本平台，谢谢";
		$result['answer'] = $an;
		$result['figure'] = $question['figure'];
		return $result;
	}

}
// JavaScript Document
function checkdata(type,val,er)
{
	var emp = /^\s*$/;
	var regPartton=/1[3-8]+\d{9}/;
	var span=document.getElementById(er);
 switch(type){
	 case 1:
	 if(emp.test(val.value)){ 
		span.innerHTML="亲,请填写手机号码!";
		//val.focus();
	 }else if(!regPartton.test(val.value)){
		 span.innerHTML="亲,手机号码有误，请重新输入!";
		// val.focus(); 
		 
	 }else{
		 span.innerHTML="该密码为验证消费密码,适用全部密码!";
		 //fr1.pwd.focus();
		 }
	 break;
	 case 2:
	 if(emp.test(val.value)){
		 span.innerHTML="亲,请填写密码!";
		 //val.focus(); 
	 }else if(val.value.length<6){
		 span.innerHTML="亲,密码不能小于6位!";
		 //val.focus(); 
	 }else{
		span.innerHTML=""; 
		//fr1.password.focus();
	 }
	 break;
	 case 3:
	 var pwd=fr1.pwd.value;
	 if(emp.test(val.value)){
		 span.innerHTML="亲,请填写确认密码!";
		 //val.focus(); 
	 }else if(val.value!=pwd){
		 span.innerHTML="亲,确认密码不正确，请重新输入!";
		// val.focus(); 
		 
	 }else{
		span.innerHTML=""; 
		//fr1.mobilephone.focus();
	 }
	 break;
	 
 }
 
 }
 
 function datasubmit(){
	 var emp = /^\s*$/;
	 var regPartton=/1[3-8]+\d{9}/;
	 var username=document.getElementById("username").value;
	 var pwd=document.getElementById("pwd").value;
	 var password=document.getElementById("password").value;
	/* var mobilephone=document.getElementById("mobilephone").value;*/
	 if(emp.test(username)){
		 alert("亲,请填写手机号码!");
		 return false;
	 }else if(!regPartton.test(username)){
		  alert("亲,手机号码有误，请重新输入!");
		 return false;
		 
	 }else if(emp.test(pwd)){
		 alert("亲,请填写密码!");
		 return false;
	 }else if(pwd.length<6){
		 alert("亲,密码不能小于6位!");
		 return false;
	 }else if(emp.test(password)){
		 alert("亲,请填写确认密码!");
		 return false;
	 }else if(password!=pwd){
		 alert("亲,确认密码不正确，请重新输入!");
		 return false;
	 }else{
		return true; 
	 }
	 
 }
 
 
 function datalogin(){
	 
	var usernames=document.getElementById("usernames").value;
	var passwords=document.getElementById("passwords").value;
	 var emp = /^\s*$/;
	 if(emp.test(usernames)){
		 alert("亲,请填写用户名!");
		 return false;
	 }else if(emp.test(passwords)){
		 alert("亲,请填写密码!");
		 return false;
	 }else{
		return true; 
	 }
	
 }
 

 
 

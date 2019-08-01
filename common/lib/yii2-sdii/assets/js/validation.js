function isEmpty (value) {
    return value === null || value === undefined || value == [] || value === '';
}

function ssn(value, messages, options) {
    if (options.skipOnEmpty && isEmpty(value)) {
	return;
    }

    var valid = valid_citizen_id(value);

    if (!valid) {
	messages.push(options.message.replace(/\{value\}/g, value));
    }
}

function valid_citizen_id (personID) {
//    if (pid.length != 13) {
//	return false;
//    }
//    var sum = 0;
//    console.log(parseInt(pid+'12'));
//    for (var i = 0; i < 12; i++){
//	sum += parseInt(pid+''+i) * (13 - i);
//    }
//    if ((11 - (sum % 11)) % 10 == parseInt(pid+'12')){
//	return true;
//    }
//    return false;
    personID = personID.replace(/-/g, '');
    console.log(personID);
    if (personID.length != 13) {
	return false;
    }
    
    if(personID.substr(0, 4) == '9999'){
        return true;
    }

    var rev = personID.split('').reverse().join(''); // reverse string ขั้นที่ 0 เตรียมตัว
    var total = 0;
    for(var i=1;i<13;i++) // ขั้นตอนที่ 1 - เอาเลข 12 หลักมา เขียนแยกหลักกันก่อน
    {
	    var mul = i +1;
	    var count = rev[i]*mul; // ขั้นตอนที่ 2 - เอาเลข 12 หลักนั้นมา คูณเข้ากับเลขประจำหลักของมัน
	    total = total + count; // ขั้นตอนที่ 3 - เอาผลคูณทั้ง 12 ตัวมา บวกกันทั้งหมด
    }
    var mod = total % 11; //ขั้นตอนที่ 4 - เอาเลขที่ได้จากขั้นตอนที่ 3 มา mod 11 (หารเอาเศษ)
    var sub = 11 - mod; //ขั้นตอนที่ 5 - เอา 11 ตั้ง ลบออกด้วย เลขที่ได้จากขั้นตอนที่ 4
    var check_digit = sub % 10; //ถ้าเกิด ลบแล้วได้ออกมาเป็นเลข 2 หลัก ให้เอาเลขในหลักหน่วยมาเป็น Check Digit
    if(rev[0] == check_digit)  // ตรวจสอบ ค่าที่ได้ กับ เลขตัวสุดท้ายของ บัตรประจำตัวประชาชน
	    return true; /// ถ้า ตรงกัน แสดงว่าถูก
    else
	    return false; // ไม่ตรงกันแสดงว่าผิด 
}
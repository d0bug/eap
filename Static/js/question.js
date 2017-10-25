var vip = vip || {};
vip.config = vip.config || {};
vip.config.optionFlags = [ 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J',
		'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X',
		'Y', 'Z', 'A2', 'B2', 'C2', 'D2', 'E2', 'F2', 'G2', 'H2', 'I2', 'J2',
		'K2', 'L2', 'M2', 'N2', 'O2', 'P2', 'Q2', 'R2', 'S2', 'T2', 'U2', 'V2',
		'W2', 'X2', 'Y2', 'Z2' ];

vip.config.questionCNNum = [ '一', '二', '三', '四', '五', '六', '七', '八', '九', '十' ];

vip.tools = {
	getOptionItem : function(id, name, content) {
		return {
			id : id,
			name : name,
			content : content
		};
	}
}
function getModelByQuestionType(type) {
	var tmpl = {};
	switch (type) {
	case 'QU1000':
		tmpl.id = 1;
		tmpl.title = '单选题';
		break;
	case 'QU1001':
		tmpl.id = 2;
		tmpl.title = '多选题';
		break;
    case 'QU1025'://针对小数选择题
        tmpl.id = 1;
        tmpl.title = '选择题';
        break;           
	case 'QU1002':
		tmpl.id = 3;
		tmpl.title = '填空题';
		break;
	case 'QU1003':
		tmpl.id = 3;
		tmpl.title = '解答题';
		break;
	case 'QU1004':
		//tmpl.id = 4;
		tmpl.id = 3;
		tmpl.title = '判断题';
		break;
	case 'QU1005':
		//tmpl.id = 5;
		tmpl.id = 3;
		tmpl.title = '判断改错题';
		break;   
	case 'QU1006':
		tmpl.id = 3;
		tmpl.title = '综合题';
		break;
	default:
		tmpl.id = 3;
		tmpl.title = '综合题';		//其它题型，默认模板
	}
	return tmpl;
}
function getTmplByQuestionTypeCode(code) {
	var tmpl = getModelByQuestionType(code);
	if (typeof tmpl.id == 'undefined')
		return '';

	var tmplName = 'question_type_' + tmpl.id;
	return tmplName;
}
function get_option_flag_name(index) {
	var flagsCount = vip.config.optionFlags.length;
	if (index > flagsCount - 1) {
		return '';
	}

	return vip.config.optionFlags[index];
}
function get_ue_ctrl(id) {
	if (id == '')
		return null;
	return UE.getEditor(id);
}
function gs_tiku_plugin() {
	return document.getElementById('tiku_plugin');
}
function addEvent(obj, name, func) {
	if (obj.attachEvent) {
		obj.attachEvent("on" + name, func);
	} else {
		obj.addEventListener(name, func, false);
	}
}
function get_random_str(len) {
	len = len || 10;
	var strs = '012345678ABCDEFGHIJKLMNOPQRSTUVWXYZ012345678';
	var maxPos = strs.length;
　　	var rs = '';
　　	for (i = 0; i < len; i++) {
　　	　　rs += strs.charAt(Math.floor(Math.random() * maxPos));
　　	}
　　	return rs;
}
function Base64() {  
	   
    // private property  
    _keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";  
   
    // public method for encoding  
    this.encode = function (input) {  
        var output = "";  
        var chr1, chr2, chr3, enc1, enc2, enc3, enc4;  
        var i = 0;  
        input = _utf8_encode(input);  
        while (i < input.length) {  
            chr1 = input.charCodeAt(i++);  
            chr2 = input.charCodeAt(i++);  
            chr3 = input.charCodeAt(i++);  
            enc1 = chr1 >> 2;  
            enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);  
            enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);  
            enc4 = chr3 & 63;  
            if (isNaN(chr2)) {  
                enc3 = enc4 = 64;  
            } else if (isNaN(chr3)) {  
                enc4 = 64;  
            }  
            output = output +  
            _keyStr.charAt(enc1) + _keyStr.charAt(enc2) +  
            _keyStr.charAt(enc3) + _keyStr.charAt(enc4);  
        }  
        return output;  
    }  
   
    // public method for decoding  
    this.decode = function (input) {  
        var output = "";  
        var chr1, chr2, chr3;  
        var enc1, enc2, enc3, enc4;  
        var i = 0;  
        input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");  
        while (i < input.length) {  
            enc1 = _keyStr.indexOf(input.charAt(i++));  
            enc2 = _keyStr.indexOf(input.charAt(i++));  
            enc3 = _keyStr.indexOf(input.charAt(i++));  
            enc4 = _keyStr.indexOf(input.charAt(i++));  
            chr1 = (enc1 << 2) | (enc2 >> 4);  
            chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);  
            chr3 = ((enc3 & 3) << 6) | enc4;  
            output = output + String.fromCharCode(chr1);  
            if (enc3 != 64) {  
                output = output + String.fromCharCode(chr2);  
            }  
            if (enc4 != 64) {  
                output = output + String.fromCharCode(chr3);  
            }  
        }  
        output = _utf8_decode(output);  
        return output;  
    }  
   
    // private method for UTF-8 encoding  
    _utf8_encode = function (string) {  
        string = string.replace(/\r\n/g,"\n");  
        var utftext = "";  
        for (var n = 0; n < string.length; n++) {  
            var c = string.charCodeAt(n);  
            if (c < 128) {  
                utftext += String.fromCharCode(c);  
            } else if((c > 127) && (c < 2048)) {  
                utftext += String.fromCharCode((c >> 6) | 192);  
                utftext += String.fromCharCode((c & 63) | 128);  
            } else {  
                utftext += String.fromCharCode((c >> 12) | 224);  
                utftext += String.fromCharCode(((c >> 6) & 63) | 128);  
                utftext += String.fromCharCode((c & 63) | 128);  
            }  
   
        }  
        return utftext;  
    }  
   
    // private method for UTF-8 decoding  
    _utf8_decode = function (utftext) {  
        var string = "";  
        var i = 0;  
        var c = c1 = c2 = 0;  
        while ( i < utftext.length ) {  
            c = utftext.charCodeAt(i);  
            if (c < 128) {  
                string += String.fromCharCode(c);  
                i++;  
            } else if((c > 191) && (c < 224)) {  
                c2 = utftext.charCodeAt(i+1);  
                string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));  
                i += 2;  
            } else {  
                c2 = utftext.charCodeAt(i+1);  
                c3 = utftext.charCodeAt(i+2);  
                string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));  
                i += 3;  
            }  
        }  
        return string;  
    }  
}
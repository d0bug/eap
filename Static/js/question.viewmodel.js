var vip = vip || {};
vip.question = vip.question || {};

(function(question) {

	ko.bindingHandlers.umeditor = {
		init : function(element, valueAccessor, allBindingsAccessor, viewModel) {
			var options = ko.utils.unwrapObservable(valueAccessor());
			//UM.getEditor(options.id).destroy();
			UM.getEditor(options.id).destroy();
			var um = UM.getEditor(options.id);
			
			um.ready(function() {
				um.setContent('AAA', false);
				//um.destroy();
			});
		}
	};

	question.viewModel = function() {
		var self = this;

		self.currentQuestionMode = ko.observable();
		
		self.html = ko.observable();
		
		self.content = vip.tools.getWordContentItem('content111', 'content122', '');
		
		self.singleOptions = ko.observableArray(vip.tools.getQuestionOptions(4, 'options', 'options[]', 'options_answer_flag[]'));
		
		self.multipleOptions = ko.observableArray(vip.tools.getQuestionOptions(4, 'options', 'options[]', 'options_answer_flag[]'));
		
		self.analysis = vip.tools.getWordContentItem('analysis122', 'analysis', '');

		self.answers = vip.tools.getWordContentItem('answers', 'answers[]', '');
		
		self.addOption = function(type, data) {
        	var options = [];
        	if (type == 'single') {
        		options = self.singleOptions;
	        }
	        else if (type == 'multiple') {
	        	options = self.multipleOptions;
	        }
        	var maxItemNum = 26;
        	var len = 0;
        	len = options().length;
        	if(len >= maxItemNum){
        		$.messager.alert('信息提示', '题目选项不能超过' + maxItemNum + '个!', 'info');
        		return ;
        	}
        	var option = vip.tools.getQuestionOption('questionoptions', len, 'question_options[]', 'question_options_flag[]');
    		options.push(option);
        }
		self.removeOption = function(type, data) {
	        if (type == 'single') {
            	self.singleOptions.remove(this);
	        }
	        else if (type == 'multiple') {
		        self.multipleOptions.remove(this);
		    }
        }
	};

}(vip.question));
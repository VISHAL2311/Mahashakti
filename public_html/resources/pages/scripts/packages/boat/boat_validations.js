/**
 * This method validates boat form fields
 * since   2016-12-24
 * author  NetQuick
 */
 var Validate = function() {
		var handleBoat = function() {
				 $("#frmBoat").validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block', // default input error message class
                                        ignore:[],
					rules: {
						title: {
							required:true,
							noSpace:true
						},
						img_id:"required",
						short_description:{
							required:true,
							noSpace:true
						},	
					
						price: {
							required:true,
							noSpace:true,
							notOnlyZero: '0'
						},
						boat_category_id: {
							required:true
						
						},
						boat_brand_id: {
							required:true
							
						},
						boat_stock_id: {
							required:true
							
						},
						boat_location: {
							required:true,
							noSpace:true
						},
						boat_condition_id: {
							required:true
							
						},
						boat_fuel_type_id: {
							required:true
							
						},
						description: {
							required:true
						},
						hull_material: {
							required:true,
							noSpace:true
						},
						year: {
							required:true,
							noSpace:true
						},
						model: {
							required:true,
							noSpace:true
						},
						length: {
							required:true,
							noSpace:true,
							notOnlyZero: '0'
						},
						beam: {
							required:true
							
						},
						length_overall: {
							required:true
						},
					
					
						display_order: {
							required: true,
							minStrict: true,
							number: true,
							noSpace: true
						},
						varMetaTitle: {
							required:true,
							noSpace:true
						},
						varMetaKeyword:{
							required:true,
							noSpace:true
						},
						varMetaDescription:{
							required:true,
							noSpace:true
						},
						'new-alias':{
							specialCharacterCheck:true,
						},
					},
					messages: {
						title: Lang.get('validation.required', { attribute: Lang.get('template.title') }),
						img_id: Lang.get('validation.required', { attribute: Lang.get('template.image') }),
						short_description: Lang.get('validation.required', { attribute: Lang.get('template.shortdescription') }),
						price: {
							required:'Price field is required.'
						},
						boat_category_id: {
							required:'Boat category field is required.'
						},
						boat_brand_id: {
							required:'Boat brand field is required.'
						},
						boat_stock_id: {
							required:'Stock field is required.'
						},
						boat_location: {
							required:'Boat location field is required.'
						},
						boat_condition_id: {
							required:'Boat condition field is required.'
						},
						boat_fuel_type_id: {
							required:'Boat fuel type field is required.'
						},
						hull_material: {
							required:'Hull material field is required.'
						},
						description: {
							required:'Description field is required.'
						},
						beam: {
							required:'Beam field is required.'
						},
						length_overall: {
							required:'Length Overall field is required.'
						},
						year: {
							required:'Year field is required.'
						},
						model: {
							required:'Model field is required.'
						},
						length: {
							required:'Length field is required.'
						},
						display_order: { required: Lang.get('validation.required', { attribute: Lang.get('template.displayorder') }) },
						varMetaTitle: Lang.get('validation.required', { attribute: Lang.get('template.metatitle') }),
						varMetaKeyword: Lang.get('validation.required', { attribute: Lang.get('template.metakeyword') }),
						varMetaDescription: Lang.get('validation.required', { attribute: Lang.get('template.metadescription') })
					},
					errorPlacement: function (error, element) { if (element.parent('.input-group').length) { error.insertAfter(element.parent()); } else if (element.hasClass('select2')) { error.insertAfter(element.next('span')); } else { error.insertAfter(element); } },
					invalidHandler: function(event, validator) { //display error alert on form submit 
								var errors = validator.numberOfInvalids();
								if (errors) {
									$.loader.close(true);
								}  
								$('.alert-danger', $('#frmBoat')).show();
					},
					highlight: function(element) { // hightlight error inputs
								$(element).closest('.form-group').addClass('has-error'); // set error class to the control group
						},
					unhighlight: function(element) {
								$(element).closest('.form-group').removeClass('has-error'); // set error class to the control group
						},
					submitHandler: function (form) {
						$('body').loader(loaderConfig);
						form.submit();
						$("button[type='submit']").attr('disabled','disabled');
						return false;
					}
				});
				$('#frmBoat input').on('keypress',function(e) {
						if (e.which == 13) {
								if ($('#frmBoat').validate().form()) {
										$('#frmBoat').submit(); //form validation success, call ajax form submit
										$("button[type='submit']").attr('disabled','disabled');
								}
								return false;
						}
				});
		}	 
		return {
				//main function to initiate the module
				init: function() {
						handleBoat();
				}
		};
}();

jQuery(document).ready(function() 
{   	 
	 Validate.init();
	 jQuery.validator.addMethod("noSpace", function(value, element){
		if(value.trim().length <= 0){
			return false; 	
		}else{
			return true; 	
		}
	}, "This field is required");

	$(document).on('keyup', '#price', function (event) {
        var input = event.currentTarget.value;
        if (input.search(/^0/) != -1) {
            $("#price").val('');
        }
    });

});
jQuery.validator.addMethod("phoneFormat", function(value, element) {
	// allow any non-whitespace characters as the host part
	return this.optional( element ) || /((\(\d{3}\) ?)|(\d{3}-))?\d{3}-\d{4}/.test( value );
}, 'Please enter a valid phone number.');

$.validator.addMethod("notOnlyZero", function (value, element, param) {
    return this.optional(element) || parseInt(value) > 0;
},"Input can not be zero.");

jQuery.validator.addMethod("minStrict", function(value, element) {
	// allow any non-whitespace characters as the host part
	if(value>0){
		return true;
	}else{
		return false;
	}
}, 'Display order must be a number higher than zero');
$('input[type=text]').on('change',function(){
	var input = $(this).val();
	var trim_input = input.trim();
	if(trim_input) {
		$(this).val(trim_input);
		return true;
	}
});

/*********** Remove Image code start Here  *************/
	$(document).ready(function() {
		if($("input[name='img_id']").val() == ''){  
					$('.removeimg').hide();
					$('.image_thumb .overflow_layer').css('display','none');
			 }else{
				 $('.removeimg').show();
					$('.image_thumb .overflow_layer').css('display','block');
			 }

		 $(document).on('click', '.removeimg', function(e) 
		 {    	 	
			$("input[name='img_id']").val('');
			$("input[name='image_url']").val('');
			$(".fileinput-new div img").attr("src",site_url+ "/resources/images/upload_file.gif");

			if($("input[name='img_id']").val() == ''){  
					$('.removeimg').hide();
					$('.image_thumb .overflow_layer').css('display','none');
				}else{
				 $('.removeimg').show();
					$('.image_thumb .overflow_layer').css('display','block');
				}			 
		});
		 //Image Sorting for image floating in all modules with multiple image
		 $('#image_sortable').sortable({
			//axis: 'x',
			stop: function (event, ui) {
				var oData = $(this).sortable('serialize');
				var aData = $(this).sortable('toArray');
				var imageIdsStrings = aData.join(',');
				var imageIds = imageIdsStrings.replace(/item-/g, '');
				$('.media_manager').parents('.fileinput.fileinput-new').find("input[name^='img_id']").val(imageIds);
			}
		});
});
/************** Remove Images Code end ****************/
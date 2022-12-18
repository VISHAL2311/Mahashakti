$(document).ready(function() 
{
	getPageList(cmsPageModuleID, pageId);
	$('.module-list').on('change',function()
	{
			var module = this.value;
			getPageList(module, pageId);
	});
	
});

function getPageList(module, pageId)
{
	$.ajax({
		url: site_url+'/powerpanel/menu/getPageList',
		data: { 'module':module,'pageId':pageId},
		type: "POST",         
		dataType: "HTML",
		success: function(data) {
			$('#page-list').html(data);
		},
		error: function() {
			console.log('error!');
		}                                 
	});
}

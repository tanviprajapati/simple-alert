jQuery('document').ready(function () {
$ = 'jQuery';   

jQuery("input[name='linkreadmore']").change(function () {
        if(jQuery(this).val() == '1' ){
            jQuery('.readmoretext').show();
        }
        else{
            jQuery('.readmoretext').hide();
            
        }
});

    jQuery('.buttonset').buttonset();

    jQuery("#skinwrap img").click(function () {
       var skinvalue = jQuery(this).attr('id');
       jQuery("#skinwrap img").removeClass('selectedimage');    
       jQuery(this).addClass('selectedimage');
       
       jQuery("#linkskin").val(skinvalue);
        
   });
   
   jQuery("a#preview_slide").click(function () { 
       postID = jQuery(this).attr('data');
     
       jQuery.ajax({
            url : preview_ajax.ajax_url,
            type : 'post',
			dataType:"html",
            data : {
                action : 'sc_preview_slide',
                post_id : postID
            },
            success : function( response ) {
                jQuery("#posts-filter").after("<div id='slidepopup' class='hidden'>" + response + "</div>");
                jQuery( "#slidepopup" ).dialog( {width: 1002 , maxWidth : 1200 , height : 500 , modal : true , classes: { "ui-dialog": "sc-dialog-slider" }});
                
       
            }
            
        });
        
    });
    
    //tabs
    jQuery( "#settings" ).tabs();
    
    jQuery("#navigationwrap .navigation-style").click(function () {
       var navigationvalue = jQuery(this).attr('data');
       jQuery("#navigationwrap .navigation-style").removeClass('selectednav');    
       jQuery(this).addClass('selectednav');
       jQuery("#navigationtext").val(navigationvalue);
       
       if(jQuery(this).hasClass("navigation-text")){
           jQuery('div.nav-text').show(); 
       }
   });
   
   
   jQuery('a.submitdelete').bind('click',function(e){
      
      var answer=confirm('Do you want to delete slide?');
      if(answer){
            
      }
      else{
            e.preventDefault();
        }
    });
    jQuery('.duplicate > a').bind('click',function(e){
      
      var answer=confirm('Do you want to duplicate slide?');
      if(answer){
            
      }
      else{
            e.preventDefault();
        }
    });
    
    jQuery('#doaction').bind('click',function(e){
      action = jQuery('#bulk-action-selector-top').val();
      if(action == 'trash'){
        var answer=confirm('Do you want to delete?');
        if(answer){

        }
        else{
              e.preventDefault();
          }
    }
    });
    jQuery('#doaction2').bind('click',function(e){
      action = jQuery('#bulk-action-selector-bottom').val();
      if(action == 'trash'){
        var answer=confirm('Do you want to delete?');
        if(answer){

        }
        else{
              e.preventDefault();
          }
        }
    });
   
   
                
    
});//end of document.ready

function checkLength(el) {
    jQuery('span.error').remove();
    jQuery('input.error').removeClass('error');
    if (el.value.length <= 0) {
    jQuery(el).addClass('error');
    jQuery('div.nav-text').after("<span class='error'>It can not be empty</span>");
  }
}
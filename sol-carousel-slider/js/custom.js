jQuery(document).ready(function() {
     
      jQuery("#skin11").solCarousel({
     
            autoPlay: 3000, //Set AutoPlay to 3 seconds
            items : 3,
            itemsDesktop : [1199,3],
            itemsDesktopSmall : [979,3]
      });
      
      jQuery("#skin22").solCarousel({
     
          navigation : true, // Show next and prev buttons
          slideSpeed : 300,
          paginationSpeed : 400,
          singleItem:false,
          items :2,
      });
      
        jQuery("#skin33").solCarousel({
     
        navigation:true, // Show next and prev buttons
        singleItem:true,
          
      });       
            
          
          
          
          
    
      
    //jQuery( ".owl-prev").html('<i class="fa fa-chevron-left"></i>');
    //jQuery( ".owl-next").html('<i class="fa fa-chevron-right"></i>');
    });
    
    

/* When the user clicks on the button, 
toggle between hiding and showing the dropdown content */
function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}






$(document).ready(function(){
    $(".hide-unsbmt-exp").click(function(){
        $(".unsbmt-exp").hide();
    });
    $("#unsbmt-exp").click(function(){
        $(".unsbmt-exp").show();
        $(".add-exp").hide();
        $(".add-bulk-exp").hide();
    });

    $(".hide-add-bulk-exp").click(function(){
        $(".add-bulk-exp").hide();
    });
    $("#add-bulk-exp").click(function(){
        $(".add-bulk-exp").show();
        $(".add-exp").hide();
        $(".unsbmt-exp").hide();
    });

    $(".hide-add-exp").click(function(){
        $(".add-exp").hide();
    });
    $("#add-exp").click(function(){
        $(".add-exp").show();
        $(".add-bulk-exp").hide();
        $(".unsbmt-exp").hide();
    });
});


// Currence Changer
$(".cxcText").click(function(){
$(this).find(".cxcEdit").css('display',"none");
$(this).find(".cxcInput").css('display',"inline-block");
//$(this).find(".cxcInput").focus();
});

$(".cxcInput").blur(function(){ 
$(this).hide(); 
$(this).siblings(".cxcEdit").html($(this).val());
$(this).siblings(".cxcEdit").show();
});

$(".cxcInput").focus(function(){ 
$(this).siblings(".cxcEdit").hide();
});



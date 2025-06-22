
// jQuery(".nav-link:not(.dropdown-toggle)").on("click", function() {
//   if ($("body").hasClass("sidebar-collapse")) {
//     $("body").removeClass("sidebar-collapse");
//   } else {
//     $("body").addClass("sidebar-collapse");
//   }
// });
  

  $(document).ready(function() {
    $('.nav-link.dropdown-toggle').click(function(e) {
      e.preventDefault();
  
      var $dropdown = $(this).parent('.dropdown');
      $dropdown.toggleClass('show-menu');
  
      // Cerrar otros menús desplegables al hacer clic en uno
      $('.nav-item.dropdown').not($dropdown).removeClass('show-menu');
    });
  
    // Cerrar el menú desplegable al hacer clic fuera de él
    $(document).on('click', function(e) {
      if (!$(e.target).closest('.nav-item.dropdown').length) {
        $('.nav-item.dropdown').removeClass('show-menu');
      }
    });
  });
  

//     $(".nav-link").on("click", function() {
//       if ($(window).width() <= 768) {
//           if ($("body").hasClass("sidebar-open")) {
//               $("body").removeClass("sidebar-open");
//           } else {
//               $("body").addClass("sidebar-open");
//           }
//       }
//   });

// const temaOscuro = () => {

//   document.querySelector('body').setAttribute('data-bs-theme', 'dark');
//   document.querySelector('#dl-icon').setAttribute('class', 'bi bi-sun-fill');

// }
  

      

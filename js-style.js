document.addEventListener("DOMContentLoaded", function () {
  var boxes = document.querySelectorAll('.box');

  boxes.forEach(function (box) {
      box.addEventListener('click', function () {
          // Change color to green
          this.classList.toggle('green');

          // Prompt for add button (you can customize this part)
          if (this.classList.contains('green')) {
              var addButton = confirm("Do you want to add something?");
              if (addButton) {
                  // Add your logic for the add button action
                  // This can include an AJAX request to a server or any other functionality
                  console.log("Add button clicked for box " + this.textContent);
              }
          }
      });
  });
});
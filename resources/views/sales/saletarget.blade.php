<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>Select Year</title>
  </head>
  <body>
    <select id="selectYear"></select>
    <script>
      const currentYear = new Date().getFullYear(); // Get current year
      const selectYear = document.getElementById("selectYear"); // Get select element

      for (let year = currentYear+1; year >= 2017; year--) { // Loop from current year to 1999
        const option = document.createElement("option"); // Create option element
        option.value = year; // Set option value to year
        option.text = year; // Set option text to year
        selectYear.add(option); // Add option to select element
      }
    </script>
  </body>
</html>

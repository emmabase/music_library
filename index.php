
<!DOCTYPE html>
<html>
<head>
    <title>Generate Barcodes</title>
    <link href="style.css" rel="stylesheet" />
    <script>
        function validateForm(event) {
            const row = document.getElementById('row').value;
            const column = document.getElementById('column').value;
            const template = event.target.value;

            if (column > 3) {
                if (template === '5160-music' || template === '5160-uniform' || template === '5160-equipment') {
                    alert('Column value cannot be greater than 3 for the selected template.');
                    event.preventDefault();
                }
            }
        }

            document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('button[value="5160-music"]').addEventListener('click', validateForm);
            document.querySelector('button[value="5160-uniform"]').addEventListener('click', validateForm);
            document.querySelector('button[value="5160-equipment"]').addEventListener('click', validateForm);
        });
    </script>
</head>
<body>
   
    <form method="get" action="process.php">
    <h1>Generate Barcodes</h1>
      <div class="input-container">
            <label for="row">Row:</label>
            <input type="number" id="row" name="row" min="1" max="10" placeholder="Enter row number">
        </div>
        <div class="input-container">
            <label for="column">Column:</label>
            <input type="number" id="column" name="column" min="1" max="4" placeholder="Enter column number">
        </div>

        <div class="button-container">
            <button type="submit" name="generate" value="5160-music">5160 Music Labels</button>
            <button type="submit" name="generate" value="5160-uniform">5160 Uniform Labels</button>
            <button type="submit" name="generate" value="5160-equipment">5160 Equipment Labels</button>
            <button type="submit" name="generate" value="5163">5163 Labels</button>
            <button type="submit" name="generate" value="5167">5167 Labels</button>
        </div>
    </form>
</body>
</html>

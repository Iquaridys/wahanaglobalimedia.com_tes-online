<!DOCTYPE html>
<html lang="en">
<head>
    <title>Tes Programming</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        form {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>Tugas NO 1</h1>

<form id="form1">
    <label for="count">Item Count:</label>
    <input type="number" id="count" name="count" value="35" min="1" required>
    <button type="button" onclick="calcItems()">Submit</button>
</form>

<h2>Hasil</h2>
<table id="table1">
    <thead>
        <tr>
            <th>Category</th>
            <th>Count</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<script>
    function calcItems() {
        const cats = [20, 12, 10, 5, 3, 1];
        const count = parseInt(document.getElementById('count').value);
        const tbody = document.getElementById('table1').getElementsByTagName('tbody')[0];
        
        tbody.innerHTML = '';

        let remaining = count;

        for (const cat of cats) {
            if (remaining <= 0) break;
            const catCount = Math.floor(remaining / cat);
            if (catCount > 0) {
                remaining -= catCount * cat;
                const row = tbody.insertRow();
                const cellCat = row.insertCell(0);
                const cellCount = row.insertCell(1);
                cellCat.textContent = cat;
                cellCount.textContent = catCount;
            }
        }
        
        if (remaining > 0) {
            const row = tbody.insertRow();
            const cellCat = row.insertCell(0);
            const cellCount = row.insertCell(1);
            cellCat.textContent = ' ';
            cellCount.textContent = remaining;
        }
    }
</script>

<br>

<h1>Tugas NO 2</h1>

<form id="form2">
    <label for="name">Enter Name:</label>
    <input type="text" id="name" name="name" value="agus salim" required>
    <button type="button" onclick="calcName()">Submit</button>
</form>

<h2>Hasil</h2>
<p id="value">0</p>

<script>
    function calcName() {
        const name = document.getElementById('name').value.toLowerCase();
        const values = { 
            'a': 1, 'b': 2, 'c': 3, 'd': 4, 'e': 5, 'f': 6, 'g': 7, 'h': 8, 
            'i': 9, 'j': 10, 'k': 11, 'l': 12, 'm': 13, 'n': 14, 'o': 15, 
            'p': 16, 'q': 17, 'r': 18, 's': 19, 't': 20, 'u': 21, 'v': 22, 
            'w': 23, 'x': 24, 'y': 25, 'z': 26 
        };
        const counted = new Set();
        let total = 0;

        for (const char of name) {
            if (char === ' ') continue;
            if (!counted.has(char)) {
                counted.add(char);
                total += values[char] || 0;
            }
        }

        document.getElementById('value').textContent = total;
    }
</script>

</body>
</html>

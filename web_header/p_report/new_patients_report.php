<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Patients Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include '../header.php'; ?>
<?php include '../sidebar.php'; ?>

<div class="main-panel">
    <div class="content">
        <div class="container">
            <h3 class="my-4">New Patients Report</h3>
            
            <!-- Form to input start date and end date -->
            <form id="newPatientsForm">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" id="startDate" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" id="endDate" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                    </div>
                </div>
            </form>
            
            <!-- Table to display patient data -->
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Patient ID</th>
                        <th>Name</th>
                        <th>Registration Date</th>
                        <th>Contact Number</th>
                    </tr>
                </thead>
                <tbody id="newPatientsTableBody">
                    <!-- Data will be inserted here dynamically using JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    <?php include '../footer.php'; ?>
</div>

<script>
// Handle form submission and fetch report data
document.getElementById('newPatientsForm').addEventListener('submit', function(event) {
    event.preventDefault();  // Prevent default form submission

    // Get the start and end dates
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // Fetch the data from the PHP backend
    fetch(`get_new_patients_report.php?startDate=${startDate}&endDate=${endDate}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('newPatientsTableBody');
            tableBody.innerHTML = '';  // Clear previous data

            // Ensure the data is an array before processing it
            if (Array.isArray(data)) {
                if (data.length === 0) {
                    // إذا لم يتم العثور على مرضى
                    const row = document.createElement('tr');
                    row.innerHTML = `<td colspan="4" class="text-center">No patients found within this period.</td>`;
                    tableBody.appendChild(row);
                } else {
                    // Populate the table with new patient data
                    data.forEach(patient => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${patient.PatientID}</td>
                            <td>${patient.FirstName} ${patient.LastName}</td>
                            <td>${patient.RegistrationDate}</td>
                            <td>${patient.ContactNumber}</td>
                        `;
                        tableBody.appendChild(row);
                    });
                }
            } else if (data.error) {
                // إذا كانت الاستجابة تحتوي على رسالة خطأ
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="4" class="text-center">Error: ${data.error}</td>`;
                tableBody.appendChild(row);
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="4" class="text-center">Unexpected data format.</td>`;
                tableBody.appendChild(row);
            }
        })
        .catch(error => {
            console.error('Error fetching patient data:', error);
            const tableBody = document.getElementById('newPatientsTableBody');
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center">Error fetching data.</td></tr>`;
        });
});
</script>

</body>
</html>

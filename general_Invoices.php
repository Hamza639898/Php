<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoices Management</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>

<?php include 'web_header/header.php'; ?>
<?php include 'web_header/sidebar.php'; ?>

<div class="main-panel">
    <div class="content">
        <div class="container-fluid">
            <h3 class="fw-bold mb-3">Invoices Management</h3>

            <!-- add button-->
            <button class="btn btn-success mb-3 btn-custom" onclick="addInvoice()"><i class="fas fa-plus"></i> Add Invoice</button>

            <!-- table of invoices-->
            <h4 class="mt-4">Invoices List</h4>
            <div id="invoicesList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Patient Name</th>
                            <th>Invoice Date</th>
                            <th>Total Amount</th>
                            <th>Amount Paid</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="invoicesTableBody">
                        <!-- AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include 'web_header/footer.php'; ?>
</div>

<script>
// Add a new invoice
function addInvoice() {
    fetch('Invoices/get_patients_for_invoices.php') 
        .then(response => response.json())
        .then(data => {
            
            const patientsOptions = data.patients.map(patient => 
                `<option value="${patient.PatientID}">${patient.PatientID} - ${patient.FullName}</option>`
            ).join('');

            
            const paymentStatusOptions = `
                <option value="Paid">Paid</option>
                <option value="Unpaid">Unpaid</option>
                <option value="Partially Paid">Partially Paid</option>
            `;

            Swal.fire({
                title: 'Add New Invoice',
                html: `
                    <div class="mb-3">
                        <label for="patientID" class="form-label">Patient</label>
                        <select id="patientID" class="form-control">
                            <option value="">Select Patient</option>
                            ${patientsOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="invoiceDate" class="form-label">Invoice Date</label>
                        <input type="date" id="invoiceDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="totalAmount" class="form-label">Total Amount</label>
                        <input type="number" id="totalAmount" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="amountPaid" class="form-label">Amount Paid</label>
                        <input type="number" id="amountPaid" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="paymentStatus" class="form-label">Payment Status</label>
                        <select id="paymentStatus" class="form-control">
                            ${paymentStatusOptions}
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add',
                preConfirm: () => {
                    const patientID = document.getElementById('patientID').value;
                    const invoiceDate = document.getElementById('invoiceDate').value;
                    const totalAmount = document.getElementById('totalAmount').value;
                    const amountPaid = document.getElementById('amountPaid').value;
                    const paymentStatus = document.getElementById('paymentStatus').value;

                    if (!patientID || !invoiceDate || !totalAmount || !amountPaid || !paymentStatus) {
                        Swal.showValidationMessage('Please fill all fields');
                    } else {
                        return fetch('Invoices/manage_invoices.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                operation: 'insert',
                                patientID,
                                invoiceDate,
                                totalAmount,
                                amountPaid,
                                paymentStatus
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status !== 'success') {
                                throw new Error(data.message);
                            }
                            return data;
                        })
                        .catch(error => {
                            Swal.showValidationMessage(`Request failed: ${error}`);
                        });
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Added!', 'Invoice has been added.', 'success');
                    loadInvoices(); 
                }
            });
        });
}

function loadInvoices() {
    fetch('Invoices/get_invoices.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('invoicesTableBody').innerHTML = data;
            applyIcons();
        });
}

// Add action icons to the table rows
function applyIcons() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        const id = row.cells[0].innerText;

        const editButton = document.createElement('button');
        editButton.classList.add('btn-edit');
        editButton.innerHTML = '<i class="fas fa-edit"></i>';
        editButton.onclick = () => editInvoice(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deleteInvoice(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}

// Edit an invoice by ID
function editInvoice(id) {
    fetch(`Invoices/get_invoice_entry.php?id=` + id)
        .then(response => response.json())
        .then(data => {
            fetch('Invoices/get_patients_for_invoices.php') // Fetch the list of patients
                .then(response => response.json())
                .then(patientsData => {
                    const patientsOptions = patientsData.patients.map(patient => {
                        const selected = patient.PatientID === data.PatientID ? 'selected' : '';
                        return `<option value="${patient.PatientID}" ${selected}>${patient.PatientID} - ${patient.FullName}</option>`;
                    }).join('');

                    const paymentStatusOptions = `
                        <option value="Paid" ${data.PaymentStatus === 'Paid' ? 'selected' : ''}>Paid</option>
                        <option value="Unpaid" ${data.PaymentStatus === 'Unpaid' ? 'selected' : ''}>Unpaid</option>
                        <option value="Partially Paid" ${data.PaymentStatus === 'Partially Paid' ? 'selected' : ''}>Partially Paid</option>
                    `;

                    Swal.fire({
                        title: 'Edit Invoice',
                        html: `
                            <div class="mb-3">
                                <label for="patientID" class="form-label">Patient</label>
                                <select id="patientID" class="form-control">
                                    <option value="">Select Patient</option>
                                    ${patientsOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="invoiceDate" class="form-label">Invoice Date</label>
                                <input type="date" id="invoiceDate" class="form-control" value="${data.InvoiceDate}">
                            </div>
                            <div class="mb-3">
                                <label for="totalAmount" class="form-label">Total Amount</label>
                                <input type="number" id="totalAmount" class="form-control" value="${data.TotalAmount}">
                            </div>
                            <div class="mb-3">
                                <label for="amountPaid" class="form-label">Amount Paid</label>
                                <input type="number" id="amountPaid" class="form-control" value="${data.AmountPaid}">
                            </div>
                            <div class="mb-3">
                                <label for="paymentStatus" class="form-label">Payment Status</label>
                                <select id="paymentStatus" class="form-control">
                                    ${paymentStatusOptions}
                                </select>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            const patientID = document.getElementById('patientID').value;
                            const invoiceDate = document.getElementById('invoiceDate').value;
                            const totalAmount = document.getElementById('totalAmount').value;
                            const amountPaid = document.getElementById('amountPaid').value;
                            const paymentStatus = document.getElementById('paymentStatus').value;

                            if (!patientID || !invoiceDate || !totalAmount || !amountPaid || !paymentStatus) {
                                Swal.showValidationMessage('Please fill all fields');
                                return false;
                            }

                            return fetch('Invoices/manage_invoices.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    operation: 'update',
                                    id: id,
                                    patientID,
                                    invoiceDate,
                                    totalAmount,
                                    amountPaid,
                                    paymentStatus
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status !== 'success') {
                                    throw new Error(data.message);
                                }
                                return data;
                            })
                            .catch(error => {
                                Swal.showValidationMessage(`Request failed: ${error}`);
                            });
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire('Updated!', 'Invoice has been updated.', 'success');
                            loadInvoices(); // Reload the invoice list after update
                        }
                    });
                });
        });
}

// Delete an invoice
function deleteInvoice(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('Invoices/manage_invoices.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    operation: 'delete',
                    id: id
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire('Deleted!', 'Invoice has been deleted.', 'success');
                    loadInvoices(); // Reload the invoice list
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Request failed: ' + error, 'error');
            });
        }
    });
}

// Load invoices when the page is loaded
window.onload = function() {
    loadInvoices();
};
</script>

</body>
</html>

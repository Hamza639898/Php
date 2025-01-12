<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payments Management</title>
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
            <h3 class="fw-bold mb-3">Payments Management</h3>

           
            <button class="btn btn-success mb-3 btn-custom" onclick="addPayment()"><i class="fas fa-plus"></i> Add Payment</button>

           
            <h4 class="mt-4">Payments List</h4>
            <div id="paymentsList">
                <table class="table table-bordered table-custom mt-3">
                    <thead>
                        <tr>
                            <th>Payment ID</th>
                            <th>Invoice ID</th>
                            <th>Payment Date</th>
                            <th>Payment Amount</th>
                            <th>Payment Method</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentsTableBody">
                      
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php include 'web_header/footer.php'; ?>
</div>

<script>

function addPayment() {
    fetch('Payments/get_invoices_for_payments.php') 
        .then(response => response.json())
        .then(data => {
            
            const invoicesOptions = data.invoices.map(invoice => 
                `<option value="${invoice.InvoiceID}">${invoice.InvoiceID}</option>`
            ).join('');

            Swal.fire({
                title: 'Add New Payment',
                html: `
                    <div class="mb-3">
                        <label for="invoiceID" class="form-label">Invoice</label>
                        <select id="invoiceID" class="form-control">
                            <option value="">Select Invoice</option>
                            ${invoicesOptions}
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="paymentDate" class="form-label">Payment Date</label>
                        <input type="date" id="paymentDate" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="paymentAmount" class="form-label">Payment Amount</label>
                        <input type="number" id="paymentAmount" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="paymentMethod" class="form-label">Payment Method</label>
                        <select id="paymentMethod" class="form-control">
                            <option value="Cash">Cash</option>
                            <option value="Credit Card">Credit Card</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                        </select>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add',
                preConfirm: () => {
                    const invoiceID = document.getElementById('invoiceID').value;
                    const paymentDate = document.getElementById('paymentDate').value;
                    const paymentAmount = document.getElementById('paymentAmount').value;
                    const paymentMethod = document.getElementById('paymentMethod').value;

                    if (!invoiceID || !paymentDate || !paymentAmount || !paymentMethod) {
                        Swal.showValidationMessage('Please fill all fields');
                    } else {
                        return fetch('Payments/manage_payments.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                operation: 'insert',
                                invoiceID,
                                paymentDate,
                                paymentAmount,
                                paymentMethod
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
                    Swal.fire('Added!', 'Payment has been added.', 'success');
                    loadPayments(); 
                }
            });
        });
}


function loadPayments() {
    fetch('Payments/get_payments.php')
        .then(response => response.text())
        .then(data => {
            document.getElementById('paymentsTableBody').innerHTML = data;
            applyIcons();
        });
}


function applyIcons() {
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach(row => {
        const id = row.cells[0].innerText;

        const editButton = document.createElement('button');
        editButton.classList.add('btn-edit');
        editButton.innerHTML = '<i class="fas fa-edit"></i>';
        editButton.onclick = () => editPayment(id);

        const deleteButton = document.createElement('button');
        deleteButton.classList.add('btn-delete');
        deleteButton.innerHTML = '<i class="fas fa-trash-alt"></i>';
        deleteButton.onclick = () => deletePayment(id);

        const actionCell = row.cells[row.cells.length - 1];
        actionCell.innerHTML = '';
        actionCell.append(editButton, deleteButton);
    });
}


function editPayment(id) {
   
    fetch(`Payments/get_payment_entry.php?id=` + id)
        .then(response => response.json())
        .then(data => {
            fetch('Payments/get_invoices_for_payments.php') 
                .then(response => response.json())
                .then(optionsData => {
                    const invoicesOptions = optionsData.invoices.map(invoice => {
                        const selected = invoice.InvoiceID === data.InvoiceID ? 'selected' : '';
                        return `<option value="${invoice.InvoiceID}" ${selected}>${invoice.InvoiceID}</option>`;
                    }).join('');

                    Swal.fire({
                        title: 'Edit Payment',
                        html: `
                            <div class="mb-3">
                                <label for="invoiceID" class="form-label">Invoice</label>
                                <select id="invoiceID" class="form-control">
                                    ${invoicesOptions}
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="paymentDate" class="form-label">Payment Date</label>
                                <input type="date" id="paymentDate" class="form-control" value="${data.PaymentDate}">
                            </div>
                            <div class="mb-3">
                                <label for="paymentAmount" class="form-label">Payment Amount</label>
                                <input type="number" id="paymentAmount" class="form-control" value="${data.PaymentAmount}" step="0.01" min="0">
                            </div>
                            <div class="mb-3">
                                <label for="paymentMethod" class="form-label">Payment Method</label>
                                <select id="paymentMethod" class="form-control">
                                    <option value="Cash" ${data.PaymentMethod === 'Cash' ? 'selected' : ''}>Cash</option>
                                    <option value="Credit Card" ${data.PaymentMethod === 'Credit Card' ? 'selected' : ''}>Credit Card</option>
                                    <option value="Bank Transfer" ${data.PaymentMethod === 'Bank Transfer' ? 'selected' : ''}>Bank Transfer</option>
                                </select>
                            </div>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Update',
                        preConfirm: () => {
                            const invoiceID = document.getElementById('invoiceID').value;
                            const paymentDate = document.getElementById('paymentDate').value;
                            const paymentAmount = document.getElementById('paymentAmount').value;
                            const paymentMethod = document.getElementById('paymentMethod').value;

                            if (!invoiceID || !paymentDate || !paymentAmount || !paymentMethod) {
                                Swal.showValidationMessage('Please fill all fields');
                                return false;
                            }

                           
                            return fetch('Payments/manage_payments.php', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json' },
                                body: JSON.stringify({
                                    operation: 'update',
                                    id: id,
                                    invoiceID,
                                    paymentDate,
                                    paymentAmount,
                                    paymentMethod
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
                            Swal.fire('Updated!', 'Payment has been updated.', 'success');
                            loadPayments(); 
                        }
                    });
                });
        });
}


function deletePayment(id) {
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
            fetch('Payments/manage_payments.php', {
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
                    Swal.fire('Deleted!', 'Payment has been deleted.', 'success');
                    loadPayments(); 
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


window.onload = function() {
    loadPayments();
};
</script>

</body>
</html>

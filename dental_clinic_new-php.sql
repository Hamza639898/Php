-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 10, 2025 at 05:39 PM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dental_clinic_new-php`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetActivePatients` ()  BEGIN
    SELECT DISTINCT p.PatientID, CONCAT(p.FirstName, ' ', p.LastName) AS Name, p.ContactNumber
    FROM patients p
    JOIN appointments a ON p.PatientID = a.PatientID
    WHERE a.Status = 'confirmed';  -- التعامل فقط مع المرضى الذين لديهم مواعيد مؤكدة
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDailyPaymentsReport` (IN `inputPaymentDate` DATE, IN `inputEndDate` DATE, IN `inputSearchQuery` VARCHAR(100))  BEGIN
    SELECT p.PaymentID, p.InvoiceID, CONCAT(pt.FirstName, ' ', pt.LastName) AS PatientName, 
           p.PaymentDate, p.PaymentAmount, p.PaymentMethod
    FROM payments p
    JOIN invoices i ON p.InvoiceID = i.InvoiceID
    JOIN patients pt ON i.PatientID = pt.PatientID
    WHERE (p.PaymentDate BETWEEN inputPaymentDate AND inputEndDate)
    OR (p.PaymentID LIKE CONCAT('%', inputSearchQuery, '%') OR p.InvoiceID LIKE CONCAT('%', inputSearchQuery, '%'));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPatientDoctorT` ()  BEGIN
    SELECT 
        p.PatientID, 
        CONCAT(p.FirstName, ' ', p.LastName) AS PatientName, 
        p.DateOfBirth,
        d.DoctorName, 
        a.AppointmentDate, 
        t.TreatmentsName, 
        t.Cost
    FROM patients p
    JOIN appointments a ON p.PatientID = a.PatientID
    JOIN doctors d ON a.DoctorID = d.DoctorID
    JOIN patient_treatments pt ON p.PatientID = pt.PatientID
    JOIN treatments t ON pt.TreatmentID = t.TreatmentID
    WHERE a.Status = 'confirmed';  -- اختيار المواعيد المؤكدة فقط
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPatientDoctorTreatmentReport` ()  BEGIN
    SELECT 
        p.PatientID, 
        CONCAT(p.FirstName, ' ', p.LastName) AS PatientName, 
        p.DateOfBirth,
        d.FirstName, 
        a.AppointmentDate, 
        t.TreatmentName, 
        t.Cost
    FROM patients p
    JOIN appointments a ON p.PatientID = a.PatientID
    JOIN dentists d ON a.DentistID=d.DentistID 
    JOIN patienttreatments pt ON p.PatientID = pt.PatientID
    JOIN treatments t ON pt.TreatmentID = t.TreatmentID
    WHERE a.Status = 'confirmed';  -- اختيار المواعيد المؤكدة فقط
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPatientMedicalReport` (IN `patientID` INT)  BEGIN
    SELECT a.AppointmentID, a.AppointmentDate, a.AppointmentTime, a.Status, 
           CONCAT(p.FirstName, ' ', p.LastName) AS PatientName,   -- ربط اسم المريض
           CONCAT(d.FirstName, ' ', d.LastName) AS DentistName, 
           t.TreatmentName, i.InvoiceID, i.TotalAmount, i.AmountPaid
    FROM appointments a
    JOIN patients p ON a.PatientID = p.PatientID                 -- ربط جدول المواعيد بجدول المرضى
    JOIN patienttreatments pt ON a.AppointmentID = pt.AppointmentID  -- ربط جدول المواعيد بجدول العلاجات
    JOIN treatments t ON pt.TreatmentID = t.TreatmentID              -- ربط جدول العلاجات بجدول العلاجات
    JOIN invoices i ON a.PatientID = i.PatientID                     -- ربط جدول المواعيد بجدول الفواتير
    JOIN dentists d ON a.DentistID = d.DentistID                     -- ربط جدول المواعيد بجدول الأطباء
    WHERE a.PatientID = patientID;                                   -- البحث عن المريض باستخدام معرف المريض
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRegisteredPatients` (IN `startDate` DATE, IN `endDate` DATE, IN `searchQuery` VARCHAR(100))  BEGIN
    SELECT PatientID, CONCAT(FirstName, ' ', LastName) AS Name, RegistrationDate, ContactNumber
    FROM patients
    WHERE RegistrationDate BETWEEN startDate AND endDate
    OR (PatientID LIKE CONCAT('%', searchQuery, '%') OR FirstName LIKE CONCAT('%', searchQuery, '%') OR LastName LIKE CONCAT('%', searchQuery, '%'));
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUnpaidInvoices` ()  BEGIN
    SELECT i.InvoiceID, CONCAT(p.FirstName, ' ', p.LastName) AS PatientName, i.InvoiceDate, 
           i.TotalAmount, i.AmountPaid, (i.TotalAmount - i.AmountPaid) AS RemainingAmount
    FROM invoices i
    JOIN patients p ON i.PatientID = p.PatientID
    WHERE i.AmountPaid < i.TotalAmount;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageAppointment` (IN `p_AppointmentID` INT, IN `p_PatientID` INT, IN `p_DentistID` INT, IN `p_AppointmentDate` DATE, IN `p_AppointmentTime` TIME, IN `p_Reason` TEXT, IN `p_Status` VARCHAR(20), IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            INSERT INTO Appointments (PatientID, DentistID, AppointmentDate, AppointmentTime, Reason, Status)
            VALUES (p_PatientID, p_DentistID, p_AppointmentDate, p_AppointmentTime, p_Reason, p_Status);
        WHEN 'UPDATE' THEN
            UPDATE Appointments
            SET PatientID = p_PatientID,
                DentistID = p_DentistID,
                AppointmentDate = p_AppointmentDate,
                AppointmentTime = p_AppointmentTime,
                Reason = p_Reason,
                Status = p_Status
            WHERE AppointmentID = p_AppointmentID;
        WHEN 'DELETE' THEN
            DELETE FROM Appointments
            WHERE AppointmentID = p_AppointmentID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageDentist` (IN `p_DentistID` INT, IN `p_FirstName` VARCHAR(50), IN `p_LastName` VARCHAR(50), IN `p_Specialty` VARCHAR(50), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(50), IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            IF EXISTS (SELECT 1 FROM Dentists WHERE Email = p_Email) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
            ELSE
                INSERT INTO Dentists (FirstName, LastName, Specialty, ContactNumber, Email)
                VALUES (p_FirstName, p_LastName, p_Specialty, p_ContactNumber, p_Email);
            END IF;
        WHEN 'UPDATE' THEN
            IF EXISTS (SELECT 1 FROM Dentists WHERE Email = p_Email AND DentistID != p_DentistID) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
            ELSE
                UPDATE Dentists
                SET FirstName = p_FirstName,
                    LastName = p_LastName,
                    Specialty = p_Specialty,
                    ContactNumber = p_ContactNumber,
                    Email = p_Email
                WHERE DentistID = p_DentistID;
            END IF;
        WHEN 'DELETE' THEN
            DELETE FROM Dentists
            WHERE DentistID = p_DentistID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageExpense` (IN `p_ExpenseID` INT, IN `p_ExpenseDate` DATE, IN `p_Description` TEXT, IN `p_Amount` DECIMAL(10,2), IN `p_Category` VARCHAR(50), IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            INSERT INTO Expenses (ExpenseDate, Description, Amount, Category)
            VALUES (p_ExpenseDate, p_Description, p_Amount, p_Category);
        WHEN 'UPDATE' THEN
            UPDATE Expenses
            SET ExpenseDate = p_ExpenseDate,
                Description = p_Description,
                Amount = p_Amount,
                Category = p_Category
            WHERE ExpenseID = p_ExpenseID;
        WHEN 'DELETE' THEN
            DELETE FROM Expenses
            WHERE ExpenseID = p_ExpenseID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageInvoice` (IN `p_InvoiceID` INT, IN `p_PatientID` INT, IN `p_InvoiceDate` DATE, IN `p_TotalAmount` DECIMAL(10,2), IN `p_AmountPaid` DECIMAL(10,2), IN `p_PaymentStatus` VARCHAR(20), IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            INSERT INTO Invoices (PatientID, InvoiceDate, TotalAmount, AmountPaid, PaymentStatus)
            VALUES (p_PatientID, p_InvoiceDate, p_TotalAmount, p_AmountPaid, p_PaymentStatus);
        WHEN 'UPDATE' THEN
            UPDATE Invoices
            SET PatientID = p_PatientID,
                InvoiceDate = p_InvoiceDate,
                TotalAmount = p_TotalAmount,
                AmountPaid = p_AmountPaid,
                PaymentStatus = p_PaymentStatus
            WHERE InvoiceID = p_InvoiceID;
        WHEN 'DELETE' THEN
            DELETE FROM Invoices
            WHERE InvoiceID = p_InvoiceID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageInvoiceDetail` (IN `p_InvoiceDetailID` INT, IN `p_InvoiceID` INT, IN `p_TreatmentID` INT, IN `p_Quantity` INT, IN `p_UnitPrice` DECIMAL(10,2), IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            INSERT INTO InvoiceDetails (InvoiceID, TreatmentID, Quantity, UnitPrice)
            VALUES (p_InvoiceID, p_TreatmentID, p_Quantity, p_UnitPrice);
        WHEN 'UPDATE' THEN
            UPDATE InvoiceDetails
            SET InvoiceID = p_InvoiceID,
                TreatmentID = p_TreatmentID,
                Quantity = p_Quantity,
                UnitPrice = p_UnitPrice
            WHERE InvoiceDetailID = p_InvoiceDetailID;
        WHEN 'DELETE' THEN
            DELETE FROM InvoiceDetails
            WHERE InvoiceDetailID = p_InvoiceDetailID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManagePatient` (IN `p_PatientID` INT, IN `p_FirstName` VARCHAR(50), IN `p_LastName` VARCHAR(50), IN `p_DateOfBirth` DATE, IN `p_Gender` VARCHAR(10), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(50), IN `p_Address` VARCHAR(255), IN `p_MedicalHistory` TEXT, IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            IF EXISTS (SELECT 1 FROM Patients WHERE Email = p_Email) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
            ELSE
                INSERT INTO Patients (FirstName, LastName, DateOfBirth, Gender, ContactNumber, Email, Address, MedicalHistory)
                VALUES (p_FirstName, p_LastName, p_DateOfBirth, p_Gender, p_ContactNumber, p_Email, p_Address, p_MedicalHistory);
            END IF;
        WHEN 'UPDATE' THEN
            IF EXISTS (SELECT 1 FROM Patients WHERE Email = p_Email AND PatientID != p_PatientID) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
            ELSE
                UPDATE Patients
                SET FirstName = p_FirstName,
                    LastName = p_LastName,
                    DateOfBirth = p_DateOfBirth,
                    Gender = p_Gender,
                    ContactNumber = p_ContactNumber,
                    Email = p_Email,
                    Address = p_Address,
                    MedicalHistory = p_MedicalHistory
                WHERE PatientID = p_PatientID;
            END IF;
        WHEN 'DELETE' THEN
            DELETE FROM Patients
            WHERE PatientID = p_PatientID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManagePatientTreatment` (IN `p_PatientTreatmentID` INT, IN `p_PatientID` INT, IN `p_TreatmentID` INT, IN `p_AppointmentID` INT, IN `p_TreatmentDate` DATE, IN `p_TreatmentNotes` TEXT, IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            INSERT INTO PatientTreatments (PatientID, TreatmentID, AppointmentID, TreatmentDate, TreatmentNotes)
            VALUES (p_PatientID, p_TreatmentID, p_AppointmentID, p_TreatmentDate, p_TreatmentNotes);
        WHEN 'UPDATE' THEN
            UPDATE PatientTreatments
            SET PatientID = p_PatientID,
                TreatmentID = p_TreatmentID,
                AppointmentID = p_AppointmentID,
                TreatmentDate = p_TreatmentDate,
                TreatmentNotes = p_TreatmentNotes
            WHERE PatientTreatmentID = p_PatientTreatmentID;
        WHEN 'DELETE' THEN
            DELETE FROM PatientTreatments
            WHERE PatientTreatmentID = p_PatientTreatmentID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManagePayment` (IN `p_PaymentID` INT, IN `p_InvoiceID` INT, IN `p_PaymentDate` DATE, IN `p_PaymentAmount` DECIMAL(10,2), IN `p_PaymentMethod` VARCHAR(50), IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            BEGIN
                INSERT INTO Payments (InvoiceID, PaymentDate, PaymentAmount, PaymentMethod)
                VALUES (p_InvoiceID, p_PaymentDate, p_PaymentAmount, p_PaymentMethod);
                UPDATE Invoices
                SET AmountPaid = AmountPaid + p_PaymentAmount
                WHERE InvoiceID = p_InvoiceID;
            END;
        WHEN 'UPDATE' THEN
            BEGIN
                DECLARE oldPaymentAmount DECIMAL(10, 2);
                SELECT PaymentAmount INTO oldPaymentAmount FROM Payments WHERE PaymentID = p_PaymentID;
                UPDATE Payments
                SET InvoiceID = p_InvoiceID,
                    PaymentDate = p_PaymentDate,
                    PaymentAmount = p_PaymentAmount,
                    PaymentMethod = p_PaymentMethod
                WHERE PaymentID = p_PaymentID;
                UPDATE Invoices
                SET AmountPaid = AmountPaid - oldPaymentAmount + p_PaymentAmount
                WHERE InvoiceID = p_InvoiceID;
            END;
        WHEN 'DELETE' THEN
            BEGIN
                DECLARE paymentAmount DECIMAL(10, 2);
                DECLARE invoiceID INT;
                SELECT PaymentAmount, InvoiceID INTO paymentAmount, invoiceID FROM Payments WHERE PaymentID = p_PaymentID;
                DELETE FROM Payments
                WHERE PaymentID = p_PaymentID;
                UPDATE Invoices
                SET AmountPaid = AmountPaid - paymentAmount
                WHERE InvoiceID = invoiceID;
            END;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageStaff` (IN `p_StaffID` INT, IN `p_FirstName` VARCHAR(50), IN `p_LastName` VARCHAR(50), IN `p_Role` VARCHAR(50), IN `p_ContactNumber` VARCHAR(15), IN `p_Email` VARCHAR(50), IN `p_Salary` DECIMAL(10,2), IN `p_HireDate` DATE, IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            IF EXISTS (SELECT 1 FROM Staff WHERE Email = p_Email) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
            ELSE
                INSERT INTO Staff (FirstName, LastName, Role, ContactNumber, Email, Salary, HireDate)
                VALUES (p_FirstName, p_LastName, p_Role, p_ContactNumber, p_Email, p_Salary, p_HireDate);
            END IF;
        WHEN 'UPDATE' THEN
            IF EXISTS (SELECT 1 FROM Staff WHERE Email = p_Email AND StaffID != p_StaffID) THEN
                SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Email already exists';
            ELSE
                UPDATE Staff
                SET FirstName = p_FirstName,
                    LastName = p_LastName,
                    Role = p_Role,
                    ContactNumber = p_ContactNumber,
                    Email = p_Email,
                    Salary = p_Salary,
                    HireDate = p_HireDate
                WHERE StaffID = p_StaffID;
            END IF;
        WHEN 'DELETE' THEN
            DELETE FROM Staff
            WHERE StaffID = p_StaffID;
    END CASE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ManageTreatment` (IN `p_TreatmentID` INT, IN `p_TreatmentName` VARCHAR(100), IN `p_Description` TEXT, IN `p_Cost` DECIMAL(10,2), IN `p_Operation` VARCHAR(10))  BEGIN
    CASE p_Operation
        WHEN 'INSERT' THEN
            INSERT INTO Treatments (TreatmentName, Description, Cost)
            VALUES (p_TreatmentName, p_Description, p_Cost);
        WHEN 'UPDATE' THEN
            UPDATE Treatments
            SET TreatmentName = p_TreatmentName,
                Description = p_Description,
                Cost = p_Cost
            WHERE TreatmentID = p_TreatmentID;
        WHEN 'DELETE' THEN
            DELETE FROM Treatments
            WHERE TreatmentID = p_TreatmentID;
    END CASE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `AppointmentID` int(11) NOT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `DentistID` int(11) DEFAULT NULL,
  `AppointmentDate` date DEFAULT NULL,
  `AppointmentTime` time DEFAULT NULL,
  `Reason` text DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`AppointmentID`, `PatientID`, `DentistID`, `AppointmentDate`, `AppointmentTime`, `Reason`, `Status`) VALUES
(3, 1, 4, '2024-08-18', '03:52:00', 'Check-up', 'Confirmed'),
(5, 9, 4, '2024-08-21', '17:04:00', 'Cleaning', 'Confirmed'),
(6, 32, 4, '2024-09-01', '20:04:00', 'Filling', 'Confirmed'),
(8, 7, 6, '2024-10-08', '17:52:00', 'Filling', 'Confirmed'),
(9, 34, 11, '2024-10-01', '19:03:00', 'Cleaning', 'Confirmed'),
(10, 1, 8, '2024-11-18', '03:00:00', 'Filling', 'Confirmed'),
(11, 9, 4, '2025-01-30', '10:53:00', 'Filling', 'Confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `dentists`
--

CREATE TABLE `dentists` (
  `DentistID` int(11) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Specialty` varchar(50) DEFAULT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dentists`
--

INSERT INTO `dentists` (`DentistID`, `FirstName`, `LastName`, `Specialty`, `ContactNumber`, `Email`) VALUES
(2, 'Alice', 'Smith', 'Orthodontist', '0987654321', 'alice.smith@example.com'),
(3, 'hamza', 'abdulkhadir', 'qalniin', '615639898', 'johndoe12@test.com'),
(4, 'yusuf', 'abdi', 'qalniin', '61563955', 'root@gmail.com'),
(6, 'John', 'abdi', 'qalniin', '61563955', 'root@gmail.com'),
(7, 'John', 'abdi', 'qalniin', '61563955', 'root@gmail.com'),
(8, 'John', 'abdi', 'qalniin', '61563955', 'root@gmail.com'),
(9, 'John', 'abdi', 'qalniin', '61563955', 'root@gmail.com'),
(10, 'yusuf7', 'ahmed', 'qalniin', '61563955', 'root@gmail.com'),
(11, 'r', 'r', 'r', '61563966', 'xmaza@64.com'),
(12, 'cali', 'hamza', 'qalniin', '5456666', 'Nasrudinabdukadir@gmail.com'),
(13, 'ahmed', 'yusfuf', 'qalniin', '615639898', 'xxamza64@gmail.com'),
(14, 'ahmed', 'yusfuf', 'qalniin', '61563', 'xxamza64@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `ExpenseID` int(11) NOT NULL,
  `ExpenseDate` date DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `Category` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`ExpenseID`, `ExpenseDate`, `Description`, `Amount`, `Category`) VALUES
(1, '2024-08-01', 'Office Supplies', '100.00', 'Office'),
(4, '2024-08-13', 'dhismo', '100.00', 'Bills');

-- --------------------------------------------------------

--
-- Table structure for table `invoicedetails`
--

CREATE TABLE `invoicedetails` (
  `InvoiceDetailID` int(11) NOT NULL,
  `InvoiceID` int(11) DEFAULT NULL,
  `TreatmentID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `UnitPrice` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoicedetails`
--

INSERT INTO `invoicedetails` (`InvoiceDetailID`, `InvoiceID`, `TreatmentID`, `Quantity`, `UnitPrice`) VALUES
(2, 2, 1, 1, '1.00');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `InvoiceID` int(11) NOT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `InvoiceDate` date DEFAULT NULL,
  `TotalAmount` decimal(10,2) DEFAULT NULL,
  `AmountPaid` decimal(10,2) DEFAULT 0.00,
  `PaymentStatus` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`InvoiceID`, `PatientID`, `InvoiceDate`, `TotalAmount`, `AmountPaid`, `PaymentStatus`) VALUES
(2, 1, '2024-08-01', '50.00', '40.00', 'Partially Paid'),
(3, 1, '2024-08-07', '100.00', '100.00', 'Paid'),
(4, 32, '2024-08-06', '600.00', '600.00', 'Paid'),
(6, 34, '2024-09-10', '50.00', '30.00', 'Partially Paid'),
(7, 7, '2025-01-22', '50.00', '40.00', 'Partially Paid'),
(8, 2, '2025-01-22', '100.00', '50.00', 'Partially Paid'),
(9, 7, '2025-01-17', '40.00', '20.00', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `PatientID` int(11) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `MedicalHistory` text DEFAULT NULL,
  `RegistrationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`PatientID`, `FirstName`, `LastName`, `DateOfBirth`, `Gender`, `ContactNumber`, `Email`, `Address`, `MedicalHistory`, `RegistrationDate`) VALUES
(1, 'Hamza', 'cabdulqaadir', '2003-10-10', 'Male', '615639898', 'xxamza64@gmil.com', 'moqdisho', 'sefSEF', NULL),
(2, 'John', 'Doe', '1990-01-01', 'Male', '123456789', 'john.doe@example.com', '123 Main St', 'No significant history', NULL),
(4, 'mohamed', 'jaamc', '2024-08-14', 'Male', '615639898', 'johndoe12@test.com', 'moqdisho', 'no customer', NULL),
(7, 'faarax', 'ahmed', '2024-08-07', 'Male', '61563955', 'root@gmail.com', 'hhhh', 'd', NULL),
(9, 'faarax', 'ahmed', '2024-08-07', 'Male', '61563955', 'root@gmail.com', 'hargaysa', 'customerf', NULL),
(10, 'faarax', 'ahmed', '2024-07-29', 'Male', '61563955', 'root@gmail.com', 'moqdisho', 'hhh', NULL),
(11, 'yuusuf', 'abdi', '2024-08-29', 'Male', '61563955', 'root@gmail.com', 'moqdisho', 'ff', NULL),
(14, 'zuhuur', 'ahmed', '2024-08-29', 'Male', '61563955', 'root@gmail.com', 'moqdisho', 'customer', NULL),
(15, 'zuhuur', 'ahmed', '2024-08-29', 'Male', '61563955', 'root@gmail.com', 'moqdisho', 'customer', NULL),
(16, 'zuhuur', 'abdi', '2024-08-29', 'Female', '61563955', 'root@gmail.com', 'moqdisho', 'n', NULL),
(17, 'zuhuur', 'abdi', '2024-08-29', 'Female', '61563955', 'root@gmail.com', 'moqdisho', 'n', NULL),
(18, 'zuhuur', 'abdi', '2024-08-29', 'Female', '61563955', 'root@gmail.com', 'moqdisho', 'n', NULL),
(19, 'zuhuur', 'abdi', '2024-08-29', 'Female', '61563955', 'root@gmail.com', 'moqdisho', 'n', NULL),
(20, 'zuhuur', 'ahmed', '2024-09-04', 'Male', '61563955', 'root@gmail.com', 'hargaysa', 'd', NULL),
(21, 'zuhuur', 'ahmed', '2024-09-04', 'Male', '61563955', 'root@gmail.com', 'hargaysa', 'd', NULL),
(22, 'zuhuur', 'ahmed', '2024-09-04', 'Male', '61563955', 'root@gmail.com', 'hargaysa', 'd', NULL),
(32, 'hamza', 'axmad', '2024-09-01', 'Male', 'xxamza@gmil.com', 'moqdisdo', 'moqdisho', 'ok', NULL),
(33, 'm', 'jaamc', '2024-08-06', 'Male', 'xxamza@gmil.com', 'admin@admin.com', 'moqdisho', 'y', NULL),
(34, 'yyy1', 'yyy2', '2024-09-09', 'Male', 'gggg@gmil.com', 'moqdisdo', 'moqdisho', 'no customer', NULL),
(35, 'hamza', 'yusfuf', '2025-01-09', 'Male', '61563', 'yaasiin64@gmail.com', 'moqdisho', 'active\n', NULL),
(36, 'hamza', 'yusfuf', '2025-01-13', 'Male', '61563', 'xxamza64@gmail.com', 'moqdisho', 'active', NULL),
(37, 'hamza', 'yusfuf', '2025-01-01', 'Male', '61563', 'xxamza64@gmail.com', 'moqdisho', 'active\n', NULL),
(38, 'hamza', 'yusfuf', '2025-01-07', 'Male', '61563', 'xxamza64@gmail.com', 'moqdisho', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patienttreatments`
--

CREATE TABLE `patienttreatments` (
  `PatientTreatmentID` int(11) NOT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `TreatmentID` int(11) DEFAULT NULL,
  `AppointmentID` int(11) DEFAULT NULL,
  `TreatmentDate` date DEFAULT NULL,
  `TreatmentNotes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `patienttreatments`
--

INSERT INTO `patienttreatments` (`PatientTreatmentID`, `PatientID`, `TreatmentID`, `AppointmentID`, `TreatmentDate`, `TreatmentNotes`) VALUES
(3, 4, 1, 3, '2024-08-15', 'daawo'),
(5, 32, 1, 6, '2024-08-11', 'ok'),
(7, 1, 4, 6, '2024-10-03', 'H'),
(8, 7, 4, 8, '2024-10-08', 'waa hal mar haweenkii'),
(10, 36, 1, 8, '2025-01-22', 'waa ok'),
(11, 15, 1, 8, '2025-01-30', '');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `PaymentID` int(11) NOT NULL,
  `InvoiceID` int(11) DEFAULT NULL,
  `PaymentDate` date DEFAULT NULL,
  `PaymentAmount` decimal(10,2) DEFAULT NULL,
  `PaymentMethod` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`PaymentID`, `InvoiceID`, `PaymentDate`, `PaymentAmount`, `PaymentMethod`) VALUES
(2, 2, '2024-08-13', '100.00', 'Cash'),
(6, 4, '2024-09-15', '60.00', 'Cash'),
(7, 6, '2024-09-09', '10.00', 'Cash');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` int(11) NOT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Role` varchar(50) DEFAULT NULL,
  `ContactNumber` varchar(15) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL,
  `Salary` decimal(10,2) DEFAULT NULL,
  `HireDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`StaffID`, `FirstName`, `LastName`, `Role`, `ContactNumber`, `Email`, `Salary`, `HireDate`) VALUES
(1, 'Bob', 'Johnson', 'Receptionist', '1122334455', 'bob.johnson@example.com', '30000.00', '2023-01-01');

-- --------------------------------------------------------

--
-- Table structure for table `treatments`
--

CREATE TABLE `treatments` (
  `TreatmentID` int(11) NOT NULL,
  `TreatmentName` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Cost` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `treatments`
--

INSERT INTO `treatments` (`TreatmentID`, `TreatmentName`, `Description`, `Cost`) VALUES
(1, 'Cleaning', 'Routine dental cleaning', '50.00'),
(4, 'IKO DHAQASHO', 'ILKAHA AYAA LAGU HAQDAA', '20.00'),
(5, 'mod1', 'waa daawo laisticmaalo ilkaha', '4.00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `security_question` varchar(255) DEFAULT NULL,
  `security_answer` varchar(255) DEFAULT NULL,
  `user_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `email`, `security_question`, `security_answer`, `user_type`) VALUES
(1, 'hamza', '123', 'xxamza64@gmail.com', '1', '11', 'admin'),
(4, 'yaasiin', '1234', 'yaasiin64@gmail.com', 'name', 'name1', 'finance'),
(10, 'hamza22', '123', 'm@gmail.com', '11', '11', 'user'),
(11, 'cabdala', '1234', 'cabdala@gmail.com', '55', '4', 'finance');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `DentistID` (`DentistID`);

--
-- Indexes for table `dentists`
--
ALTER TABLE `dentists`
  ADD PRIMARY KEY (`DentistID`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`ExpenseID`);

--
-- Indexes for table `invoicedetails`
--
ALTER TABLE `invoicedetails`
  ADD PRIMARY KEY (`InvoiceDetailID`),
  ADD KEY `InvoiceID` (`InvoiceID`),
  ADD KEY `TreatmentID` (`TreatmentID`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`InvoiceID`),
  ADD KEY `PatientID` (`PatientID`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`PatientID`);

--
-- Indexes for table `patienttreatments`
--
ALTER TABLE `patienttreatments`
  ADD PRIMARY KEY (`PatientTreatmentID`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `TreatmentID` (`TreatmentID`),
  ADD KEY `AppointmentID` (`AppointmentID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentID`),
  ADD KEY `InvoiceID` (`InvoiceID`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`StaffID`);

--
-- Indexes for table `treatments`
--
ALTER TABLE `treatments`
  ADD PRIMARY KEY (`TreatmentID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `dentists`
--
ALTER TABLE `dentists`
  MODIFY `DentistID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `ExpenseID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoicedetails`
--
ALTER TABLE `invoicedetails`
  MODIFY `InvoiceDetailID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `InvoiceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `patienttreatments`
--
ALTER TABLE `patienttreatments`
  MODIFY `PatientTreatmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `StaffID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `treatments`
--
ALTER TABLE `treatments`
  MODIFY `TreatmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`DentistID`) REFERENCES `dentists` (`DentistID`) ON DELETE CASCADE;

--
-- Constraints for table `invoicedetails`
--
ALTER TABLE `invoicedetails`
  ADD CONSTRAINT `invoicedetails_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoices` (`InvoiceID`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoicedetails_ibfk_2` FOREIGN KEY (`TreatmentID`) REFERENCES `treatments` (`TreatmentID`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE SET NULL;

--
-- Constraints for table `patienttreatments`
--
ALTER TABLE `patienttreatments`
  ADD CONSTRAINT `patienttreatments_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE,
  ADD CONSTRAINT `patienttreatments_ibfk_2` FOREIGN KEY (`TreatmentID`) REFERENCES `treatments` (`TreatmentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `patienttreatments_ibfk_3` FOREIGN KEY (`AppointmentID`) REFERENCES `appointments` (`AppointmentID`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`InvoiceID`) REFERENCES `invoices` (`InvoiceID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

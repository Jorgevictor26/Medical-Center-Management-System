# MedCare API

REST API for the **MedCare Medical Center Management System**.

MedCare is a medical center management platform designed to replace manual paper-based workflows for:

- Patient registration
- Doctor management
- Consultations
- Medical procedures
- Payments
- Patient history
- Waiting queue
- Financial tracking
- Reports
- Dashboard

---

## 1. Technology Stack

- **Backend:** Laravel
- **API:** REST API
- **Database:** MySQL
- **Response format:** JSON
- **Authentication:** Laravel Sanctum (planned, not implemented yet)
- **Frontend:** Angular
- **Architecture:** Request → Controller → Service → Repository → Model → Database

---

## 2. Base URL

Local development:

```text
http://127.0.0.1:8000/api
```

All API endpoints are prefixed with:

```text
/api
```

Example:

```http
GET http://127.0.0.1:8000/api/patients
```

---

## 3. Main Entities

### Patient

```text
id
name
id_number
date_of_birth
gender
phone
address
created_at
updated_at
```

### Doctor

```text
id
name
specialization
phone
email
status
created_at
updated_at
```

### Procedure

```text
id
name
description
price
status
created_at
updated_at
```

### Consultation

```text
id
patient_id
doctor_id
consultation_date
observation
status
created_at
updated_at
```

Possible statuses:

```text
waiting
in_progress
completed
cancelled
```

### Consultation Procedure

```text
id
consultation_id
procedure_id
quantity
unit_price
tooth
observation
created_at
updated_at
```

`unit_price` stores the price at the time the procedure was added, preserving historical prices.

### Payment

```text
id
consultation_id
amount
payment_method
payment_date
notes
created_at
updated_at
```

Supported payment methods:

```text
cash
card
transfer
other
```

A consultation can have multiple payments.

---

## 4. Relationships

```text
Patient
   │
   └── 1:N ── Consultation
                  │
                  ├── N:1 ── Doctor
                  │
                  ├── 1:N ── ConsultationProcedure
                  │                  │
                  │                  └── N:1 ── Procedure
                  │
                  └── 1:N ── Payment
```

---

# 5. Patients API

## List Patients

```http
GET /api/patients
```

Returns all registered patients.

## Create Patient

```http
POST /api/patients
```

Example:

```json
{
    "name": "John Doe",
    "id_number": "123456789",
    "date_of_birth": "1998-05-20",
    "gender": "male",
    "phone": "923000000",
    "address": "Luanda"
}
```

## Get Patient

```http
GET /api/patients/{patient}
```

Example:

```http
GET /api/patients/1
```

## Update Patient

```http
PUT /api/patients/{patient}
```

or:

```http
PATCH /api/patients/{patient}
```

Example:

```json
{
    "name": "John Victor",
    "phone": "923111111"
}
```

## Delete Patient

```http
DELETE /api/patients/{patient}
```

## Patient History

```http
GET /api/patients/{patient}/history
```

Returns:

- Patient
- Consultations
- Doctors
- Procedures
- Payments
- Total
- Paid
- Outstanding

---

# 6. Doctors API

## List Doctors

```http
GET /api/doctors
```

## Create Doctor

```http
POST /api/doctors
```

Example:

```json
{
    "name": "Dr. John Smith",
    "specialization": "General Medicine",
    "phone": "923000000",
    "email": "doctor@example.com",
    "status": true
}
```

## Get Doctor

```http
GET /api/doctors/{doctor}
```

## Update Doctor

```http
PUT /api/doctors/{doctor}
```

or:

```http
PATCH /api/doctors/{doctor}
```

## Delete Doctor

```http
DELETE /api/doctors/{doctor}
```

---

# 7. Procedures API

## List Procedures

```http
GET /api/procedures
```

## Create Procedure

```http
POST /api/procedures
```

Example:

```json
{
    "name": "Dental Cleaning",
    "description": "Professional dental cleaning",
    "price": 25000,
    "status": true
}
```

## Get Procedure

```http
GET /api/procedures/{procedure}
```

## Update Procedure

```http
PUT /api/procedures/{procedure}
```

or:

```http
PATCH /api/procedures/{procedure}
```

## Delete Procedure

```http
DELETE /api/procedures/{procedure}
```

---

# 8. Consultations API

## List Consultations

```http
GET /api/consultations
```

Returns consultations with their related patient and doctor.

## Create Consultation

```http
POST /api/consultations
```

Example:

```json
{
    "patient_id": 1,
    "doctor_id": 1
}
```

The backend automatically sets:

```text
consultation_date = current date/time
status = waiting
```

## Get Consultation

```http
GET /api/consultations/{consultation}
```

Returns:

- Patient
- Doctor
- Procedures
- Payments

## Update Consultation

```http
PUT /api/consultations/{consultation}
```

or:

```http
PATCH /api/consultations/{consultation}
```

Used for editable consultation information such as observations.

Completed and cancelled consultations cannot be edited.

## Delete Consultation

```http
DELETE /api/consultations/{consultation}
```

---

# 9. Waiting Queue

```http
GET /api/consultations/waiting
```

Returns consultations currently waiting for the doctor.

Waiting consultations are ordered by consultation date.

---

# 10. Consultation Status

```http
PATCH /api/consultations/{consultation}/status
```

Example:

```json
{
    "status": "in_progress"
}
```

Allowed workflow:

```text
waiting
   ↓
in_progress
   ↓
completed
```

Cancellation:

```text
waiting ───────→ cancelled

in_progress ───→ cancelled
```

Invalid transitions are rejected.

Examples:

```text
completed → waiting        ❌
completed → in_progress    ❌
cancelled → waiting        ❌
```

---

# 11. Add Procedure to Consultation

```http
POST /api/consultations/{consultation}/procedures
```

Example:

```json
{
    "procedure_id": 1,
    "quantity": 2,
    "tooth": "16",
    "observation": "Procedure completed successfully."
}
```

The client does not provide `unit_price`.

The backend obtains the current procedure price and stores it as the historical `unit_price`.

Procedures can only be added while:

```text
status = in_progress
```

A consultation can have zero, one, or multiple procedures.

---

# 12. Consultation Financial Summary

```http
GET /api/consultations/{consultation}/financial-summary
```

Returns:

- Total
- Paid
- Outstanding
- Payment status

Example:

```json
{
    "total": 75500.08,
    "paid": 15000,
    "outstanding": 60500.08,
    "payment_status": "partially_paid"
}
```

Possible payment statuses:

```text
unpaid
partially_paid
paid
```

Financial calculation:

```text
Total = Σ(quantity × unit_price)

Paid = Σ(payments)

Outstanding = Total - Paid
```

---

# 13. Payments API

## List Payments

```http
GET /api/payments
```

## Register Payment

```http
POST /api/payments
```

Example:

```json
{
    "consultation_id": 1,
    "amount": 15000,
    "payment_method": "cash",
    "payment_date": "2026-10-05",
    "notes": "Partial payment"
}
```

The payment amount cannot exceed the consultation's outstanding balance.

Multiple payments are allowed for the same consultation.

## Get Payment

```http
GET /api/payments/{payment}
```

## Delete Payment

```http
DELETE /api/payments/{payment}
```

Payments do not have a normal update endpoint to prevent casual modification of financial records.

---

# 14. Dashboard API

```http
GET /api/dashboard
```

Returns current dashboard information.

Example:

```json
{
    "date": "2026-10-05",
    "summary": {
        "patients": 20,
        "consultations_today": 1,
        "completed_today": 1,
        "waiting": 0,
        "revenue_today": 37750.04,
        "paid_today": 15000,
        "outstanding_today": 22750.04
    },
    "waiting_patients": []
}
```

Dashboard includes:

- Total registered patients
- Today's consultations
- Today's completed consultations
- Current waiting patients
- Today's revenue
- Today's payments
- Today's outstanding balance
- Waiting patient details

---

# 15. Reports API

```http
GET /api/reports
```

The report endpoint accepts a date range.

Required parameters:

```text
from
to
```

Example:

```http
GET /api/reports?from=2026-10-01&to=2026-10-05
```

The same endpoint supports:

- Daily reports
- Weekly reports
- Monthly reports
- Custom date ranges

## Report Structure

### Period

```json
{
    "from": "2026-10-01",
    "to": "2026-10-05"
}
```

### Summary

Includes:

```text
patients
consultations
completed
cancelled
waiting
in_progress
revenue
paid
outstanding
```

### Doctor Summary

Includes:

```text
doctor_id
doctor
patients
consultations
revenue
```

### Procedure Summary

Includes:

```text
procedure_id
procedure
quantity
revenue
```

### Consultation Details

Each consultation includes:

```text
consultation_id

patient
    id
    name
    id_number

doctor
    id
    name
    specialization

date
status

procedures
    name
    quantity
    unit_price
    total
    tooth
    observation

total
paid
outstanding
payment_status

payments
    amount
    payment_method
    payment_date
    notes
```

---

# 16. Financial Rules

## Consultation Total

```text
Total = Σ(quantity × unit_price)
```

Example:

```text
Procedure price = 37,750.04
Quantity = 2

Total = 37,750.04 × 2
      = 75,500.08
```

## Outstanding Balance

```text
Outstanding = Total - Paid
```

Example:

```text
Total = 75,500.08
Paid = 15,000

Outstanding = 60,500.08
```

## Payment Status

```text
Paid = 0
→ unpaid

Paid > 0 AND Outstanding > 0
→ partially_paid

Outstanding = 0
→ paid
```

---

# 17. Business Rules

## Patients

- `id_number` must be unique.
- A patient can have multiple consultations.

## Doctors

- A doctor can have multiple consultations.
- Inactive doctors cannot be assigned to new consultations.

## Procedures

- Procedures have a price.
- Inactive procedures cannot be added to new consultations.
- Historical procedure prices are stored in `consultation_procedures`.

## Consultations

- Every consultation belongs to one patient.
- Every consultation belongs to one doctor.
- A consultation can have zero, one, or multiple procedures.
- A consultation can have multiple payments.
- Completed consultations cannot be edited.
- Cancelled consultations cannot be edited.
- Procedures can only be added while the consultation is `in_progress`.

## Payments

- A payment belongs to one consultation.
- Multiple payments are allowed.
- A payment cannot exceed the outstanding balance.
- Payments do not have a normal update operation.

## Reports

- Reports are generated dynamically from the database.
- Reports are not stored as database entities.
- The same report endpoint supports different date ranges.

---

# 18. Current API Routes

```text
GET     /api/consultations
POST    /api/consultations
GET     /api/consultations/waiting
GET     /api/consultations/{consultation}
PUT     /api/consultations/{consultation}
PATCH   /api/consultations/{consultation}
DELETE  /api/consultations/{consultation}

GET     /api/consultations/{consultation}/financial-summary
POST    /api/consultations/{consultation}/procedures
PATCH   /api/consultations/{consultation}/status

GET     /api/dashboard

GET     /api/doctors
POST    /api/doctors
GET     /api/doctors/{doctor}
PUT     /api/doctors/{doctor}
PATCH   /api/doctors/{doctor}
DELETE  /api/doctors/{doctor}

GET     /api/patients
POST    /api/patients
GET     /api/patients/{patient}
PUT     /api/patients/{patient}
PATCH   /api/patients/{patient}
DELETE  /api/patients/{patient}

GET     /api/patients/{patient}/history

GET     /api/payments
POST    /api/payments
GET     /api/payments/{payment}
DELETE  /api/payments/{payment}

GET     /api/procedures
POST    /api/procedures
GET     /api/procedures/{procedure}
PUT     /api/procedures/{procedure}
PATCH   /api/procedures/{procedure}
DELETE  /api/procedures/{procedure}

GET     /api/reports
```

---

# 19. API Architecture

```text
                    Angular
                       │
                       │ HTTP / JSON
                       ▼
                Laravel REST API
                       │
             ┌─────────┴─────────┐
             │                   │
        Controllers          Form Requests
             │
             ▼
          Services
             │
             ▼
        Repositories
             │
             ▼
           Models
             │
             ▼
            MySQL
```

---

# 20. Main Business Workflow

```text
Patient arrives
      ↓
Search / Register Patient
      ↓
Start Consultation
      ↓
Waiting Queue
      ↓
Doctor opens Consultation
      ↓
Status: in_progress
      ↓
Record observations
      ↓
Add Procedures
      ↓
Complete Consultation
      ↓
Register Payment
      ↓
Calculate Balance
      ↓
Patient History / Reports / Dashboard
```

---

# 21. Backend MVP Status

- [x] Patients
- [x] Doctors
- [x] Procedures
- [x] Consultations
- [x] Consultation procedures
- [x] Payments
- [x] Financial calculations
- [x] Consultation workflow
- [x] Waiting queue
- [x] Patient history
- [x] Reports
- [x] Dashboard
- [ ] Authentication and roles
- [ ] Automated tests

Authentication and role-based authorization will be implemented after the current MVP functionality has been fully verified.

---

# 22. Frontend

The frontend will be developed separately using **Angular**.

Angular will consume the Laravel REST API and provide:

- Dashboard
- Patient management
- Doctor management
- Procedure management
- Reception workflow
- Doctor consultation interface
- Payment interface
- Patient history
- Reports
- PDF export
- Excel export
- CSV export

The backend provides the data and business logic, while Angular handles presentation and user interaction.

---

# 23. Export Strategy

The report is already provided by Laravel as JSON.

Angular will organize the report visually and provide export actions:

```text
Laravel API
     ↓
JSON Report
     ↓
Angular Report Page
     ├── View
     ├── Print
     ├── Export PDF
     ├── Export Excel
     └── Export CSV
```

The report does not need to be duplicated as a Blade view.

For the MVP, report export will be handled from the Angular frontend.

---

# 24. Future Features

Possible future versions:

- Authentication and role-based access
- Appointment scheduling
- Multiple clinic branches
- Insurance/convention management
- Notifications
- WhatsApp integration
- Email notifications
- Inventory/pharmacy
- Laboratory management
- Advanced analytics
- Audit logs
- Database backup management
- Online payments

---

# 25. Development Roadmap

```text
Backend
   │
   ├── Core modules                    ✅
   ├── Financial logic                 ✅
   ├── Consultation workflow           ✅
   ├── Patient history                 ✅
   ├── Reports                         ✅
   ├── Dashboard                       ✅
   │
   └── MVP verification                ← CURRENT
              │
              ▼
       Authentication + Roles
              │
              ▼
        Angular Frontend
              │
              ├── Dashboard
              ├── Patients
              ├── Doctors
              ├── Procedures
              ├── Reception
              ├── Doctor workflow
              ├── Payments
              └── Reports
              │
              ▼
        PDF / Excel / CSV
              │
              ▼
        MVP COMPLETE
```

---

## License

This project is currently under private development.

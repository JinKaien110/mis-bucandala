Got it. Based on your MIS scope (resident records + document logs/requests + service schedules + admin workflows), here’s a solid **API path/list** you can build first in Laravel + MySQL. I’ll write it as **REST endpoints** under:

`/api/v1/...`

(You can implement with **Laravel Sanctum** for auth.)

---

## 1) Auth & Account

* `POST /auth/register` (optional: if residents can create accounts)
* `POST /auth/login`
* `POST /auth/logout`
* `GET  /auth/me`
* `PUT  /auth/me` (update profile)
* `PUT  /auth/password` (change password)

---

## 2) Roles & Users (Admin side)

* `GET  /users`
* `POST /users`
* `GET  /users/{id}`
* `PUT  /users/{id}`
* `PATCH /users/{id}/status` (activate/deactivate)
* `GET  /roles`
* `POST /roles`
* `PUT  /roles/{id}`
* `GET  /permissions` (optional if you’ll do RBAC)

---

## 3) Residents Module (Core)

* `GET  /residents` (filters: purok, age, sex, status, search)
* `POST /residents`
* `GET  /residents/{id}`
* `PUT  /residents/{id}`
* `PATCH /residents/{id}/status` (active/inactive/deceased/moved-out)
* `DELETE /residents/{id}` (usually soft delete)

**Resident attachments**

* `POST /residents/{id}/attachments`
* `GET  /residents/{id}/attachments`
* `DELETE /residents/{id}/attachments/{attachmentId}`

---

## 4) Households / Families (optional but common in barangay MIS)

* `GET  /households`
* `POST /households`
* `GET  /households/{id}`
* `PUT  /households/{id}`
* `POST /households/{id}/members` (attach resident)
* `DELETE /households/{id}/members/{residentId}`

---

## 5) Barangay Officials & Staff

* `GET  /officials`
* `POST /officials`
* `GET  /officials/{id}`
* `PUT  /officials/{id}`
* `PATCH /officials/{id}/term` (start/end dates)
* `PATCH /officials/{id}/status`

---

## 6) Document Requests (Clearances, Certificates, Indigency, etc.)

**Document types**

* `GET  /document-types`
* `POST /document-types`
* `PUT  /document-types/{id}`
* `PATCH /document-types/{id}/status`

**Requests (Citizen → Barangay)**

* `GET  /document-requests` (filters: status, type, date range)
* `POST /document-requests`
* `GET  /document-requests/{id}`
* `PUT  /document-requests/{id}`

**Workflow**

* `PATCH /document-requests/{id}/status` (pending → processing → ready → released / rejected)
* `POST  /document-requests/{id}/release` (marks released + who released)
* `POST  /document-requests/{id}/print` (log print action; optional)

**Payments (if may fee)**

* `POST /document-requests/{id}/payments`
* `GET  /document-requests/{id}/payments`

---

## 7) Blotter / Incident Records (if included)

* `GET  /blotters`
* `POST /blotters`
* `GET  /blotters/{id}`
* `PUT  /blotters/{id}`
* `PATCH /blotters/{id}/status` (open/ongoing/settled/archived)
* `POST /blotters/{id}/attachments`
* `GET  /blotters/{id}/attachments`

---

## 8) Barangay Services / Programs (optional)

* `GET  /services`
* `POST /services`
* `PUT  /services/{id}`
* `PATCH /services/{id}/status`

**Service availment/logs**

* `GET  /service-logs`
* `POST /service-logs` (resident_id + service_id + notes)
* `GET  /service-logs/{id}`
* `PUT  /service-logs/{id}`

---

## 9) Schedules (Barangay service schedules / appointments)

* `GET  /schedules` (filters: date range, category)
* `POST /schedules`
* `GET  /schedules/{id}`
* `PUT  /schedules/{id}`
* `DELETE /schedules/{id}`
* `PATCH /schedules/{id}/status` (active/cancelled)

---

## 10) Announcements / Notices

* `GET  /announcements`
* `POST /announcements`
* `GET  /announcements/{id}`
* `PUT  /announcements/{id}`
* `PATCH /announcements/{id}/status` (published/draft/archived)

---

## 11) Reports & Dashboard

* `GET /dashboard/summary` (counts: residents, requests by status, etc.)
* `GET /reports/residents` (export)
* `GET /reports/document-requests`
* `GET /reports/blotters`
* `GET /reports/services`
  *(You can add query params for date ranges and filters.)*

---

## 12) Audit Logs (important for MIS)

* `GET /audit-logs` (filters: user, module, date range)
* `GET /audit-logs/{id}`

---

## 13) Settings / Reference Data

* `GET /settings/barangay-profile` (name, logo, address header for documents)
* `PUT /settings/barangay-profile`
* `GET /reference/puroks`
* `POST /reference/puroks`
* `PUT /reference/puroks/{id}`
* `GET /reference/civil-statuses` (optional)

---

# Recommended “Build Order” (path)

1. **Auth + Roles/Users**
2. **Residents** (CRUD + filters + validation)
3. **Document Types + Document Requests + Status workflow + Release log**
4. **Schedules + Announcements**
5. **Reports + Audit Logs**
6. (Optional) **Households / Blotter / Services**

---

If you want, I can also generate the **database tables + relationships** for this API list (Laravel migrations-ready structure), and a clean **route grouping** layout (`Route::prefix('v1')->group(...)`).


3️⃣ Resident Information Fields

Confirm what you want stored.

Typical barangay fields:

Full name

Sex

Birthdate

Age (auto-compute?)

Address

Purok

Civil status

Citizenship

Contact number

Email

Voter status

Occupation

Household ID

Status (active / deceased / moved out)


Perfect — with what you confirmed, here’s the **clean “Phase 1 → Phase 2” API path** for your Barangay MIS (Laravel + MySQL, XAMPP). I’ll keep it aligned with **Admin/Staff-first**, and keep **Resident dashboard + Payments** as optional later.

---

## Assumptions from your reply

* **Users:** Barangay Admin + Staff (primary), **Super Admin** exists but low priority
* **Resident dashboard:** not sure yet → we’ll design so it’s optional
* **Household ID:** not required → we’ll use **address fields** (block/lot, street, purok) and keep household grouping optional
* **Documents:** all common ones (final templates on Wednesday)
* **Payments:** unsure → design as optional module
* **Blotter:** YES
* **Scheduling:** mainly announcements/events (not full appointment booking)
* **Reports:** later phase
* **Deployment:** XAMPP local

---

# API List (Recommended)

## 1) Auth (Admin/Staff login)

* `POST /api/v1/auth/login`
* `POST /api/v1/auth/logout`
* `GET  /api/v1/auth/me`
* `PUT  /api/v1/auth/me` (profile)
* `PUT  /api/v1/auth/password`

> Registration endpoint is optional (you can seed users in DB for now).

---

## 2) Users Management (Admin-only)

* `GET  /api/v1/users` (list staff/admin)
* `POST /api/v1/users` (create staff)
* `GET  /api/v1/users/{id}`
* `PUT  /api/v1/users/{id}`
* `PATCH /api/v1/users/{id}/status` (active/inactive)

**Roles (simple)**

* `GET  /api/v1/roles` (admin, staff, super_admin)
* `PATCH /api/v1/users/{id}/role`

---

## 3) Residents (Core module)

* `GET  /api/v1/residents` (filters + search)
* `POST /api/v1/residents`
* `GET  /api/v1/residents/{id}`
* `PUT  /api/v1/residents/{id}`
* `PATCH /api/v1/residents/{id}/status`
  Suggested statuses: `active, moved_out, deceased, inactive`

**Resident attachments (optional but useful)**

* `POST /api/v1/residents/{id}/attachments`
* `GET  /api/v1/residents/{id}/attachments`
* `DELETE /api/v1/residents/{id}/attachments/{attachmentId}`

✅ About **Household ID**: you’re right it’s not commonly used by name, but it’s just an internal grouping (same address/family). We can **skip it** now and add later as `household_code` if your panel wants family grouping.

---

## 4) Document Issuance (Requests + Logs)

### Document Types (setup)

* `GET  /api/v1/document-types`
* `POST /api/v1/document-types`
* `PUT  /api/v1/document-types/{id}`
* `PATCH /api/v1/document-types/{id}/status` (active/inactive)

### Document Requests / Logs

Since residents may not have a dashboard yet, **staff can create requests for residents**.

* `GET  /api/v1/document-requests` (filter by type, status, dates)
* `POST /api/v1/document-requests` (staff creates for resident)
* `GET  /api/v1/document-requests/{id}`
* `PUT  /api/v1/document-requests/{id}`

### Workflow

* `PATCH /api/v1/document-requests/{id}/status`

Suggested statuses that work whether or not you have approval:

* `pending` → `processing` → `ready` → `released`
* (optional) `rejected`, `cancelled`

### Release + Printing logs (good for auditing)

* `POST /api/v1/document-requests/{id}/release`
* `POST /api/v1/document-requests/{id}/print` (log “printed by”)

---

## 5) Blotter Module (Incident records)

* `GET  /api/v1/blotters` (filters: status/date/search)
* `POST /api/v1/blotters`
* `GET  /api/v1/blotters/{id}`
* `PUT  /api/v1/blotters/{id}`
* `PATCH /api/v1/blotters/{id}/status`

Suggested statuses:

* `filed, ongoing, settled, archived`

Attachments (optional):

* `POST /api/v1/blotters/{id}/attachments`
* `GET  /api/v1/blotters/{id}/attachments`

---

## 6) Announcements (Scheduling as announcements/events)

* `GET  /api/v1/announcements`
* `POST /api/v1/announcements`
* `GET  /api/v1/announcements/{id}`
* `PUT  /api/v1/announcements/{id}`
* `PATCH /api/v1/announcements/{id}/status` (`draft, published, archived`)

---

# Later Phase (after core works)

## A) Payments (only if client confirms)

* `POST /api/v1/document-requests/{id}/payments`
* `GET  /api/v1/document-requests/{id}/payments`

Supports:

* `method` = cash (default), gcash (later)
* `amount`, `receipt_no`, `paid_at`, `received_by`

## B) Reports + Dashboard

* `GET /api/v1/dashboard/summary`
* `GET /api/v1/reports/residents`
* `GET /api/v1/reports/documents`
* `GET /api/v1/reports/blotters`

## C) Resident portal (optional)

* `POST /api/v1/resident/auth/register`
* `POST /api/v1/resident/auth/login`
* `GET  /api/v1/resident/requests`
* `POST /api/v1/resident/requests`

---

# Best Build Order (so you finish faster)

1. Auth + Users
2. Residents CRUD + filters
3. Document Types + Document Requests + Status flow + release/print logs
4. Blotter
5. Announcements
6. Reports/Dashboard
7. Optional: Payments + Resident portal

---

If you want, next message I’ll give you the **database tables (migrations-ready)** for:
`users, residents, document_types, document_requests, blotters, announcements, attachments, audit_logs`
(and keep payments/resident portal optional).


Add these (small but high-impact):

PATCH /document-requests/{id}/status (make sure you really use it end-to-end)

POST /document-requests/{id}/print (log who printed + timestamp)

POST /document-requests/{id}/release (released_by + released_at)

(optional but recommended) POST /document-requests/{id}/payments (cash + OR no.)

This turns “request module” into a real barangay document issuance system.
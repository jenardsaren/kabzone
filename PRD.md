# PRD: Learning Center Session Management System (Laravel 12 + Breeze + Tailwind)

## 1. Summary
Build a role-based web system for a learning center to manage client sessions, therapist notes, assistant tasks, and client-facing completed session history.

The application will be server-rendered using Laravel 12 + Breeze (Blade), Tailwind CSS, Alpine.js, and FullCalendar for Front Desk scheduling.

Core business outcomes:
- Front Desk can register clients and schedule sessions (single or daily repeat up to 30 days).
- Therapists can manage session details, assign assistants, create tasks, and complete sessions.
- Assistants can update task progress.
- Clients can view only completed sessions with details/notes.
- Admin can manage staff accounts and monitor attended-session metrics and recent session activity.

### Display Naming (cosmetic)
- Therapist roles continue to use the `therapist` enum/backend identifiers, but the UI now shows “OT” wherever that role label appears to users (forms, lists, dashboards, etc.).
- Assistant roles remain `assistant` in the backend while the UI labels them as “KSA”; dropdown values, policies, and routes stay unchanged.

## 2. Locked Decisions (from discovery)
- Account creation: internal provisioning.
- User management scope: Admin manages staff accounts; Front Desk manages client accounts.
- Credentials: temporary password at creation, forced password reset on first login.
- Scheduling conflicts: strict no-overlap for the same therapist and same client.
- Repeat scheduling: skip conflicted dates and show skipped-date summary.
- Session statuses (final): `pending`, `completed`, `cancelled`.
- Status transition authority: Therapist and Admin.
- Client visibility: completed sessions only.
- Attended KPI logic: session is attended only if `status = completed` and has at least 1 task.
- Assistant assignment: optional at scheduling, required before completion.
- Completed sessions: read-only except Admin override.
- UI architecture: Blade + controllers + Form Requests.
- Calendar: FullCalendar.
- Notifications: out of v1 scope.
- Timezone: `Asia/Manila`.
- Operating hours: Mon–Fri 8:00 AM–8:00 PM, Sun 1:00 PM–8:00 PM, Sat closed.

## 3. In Scope / Out of Scope
In scope:
- Role-based dashboards and permissions for Admin, Front Desk, Therapist, Assistant, Client.
- Client registration and staff account management.
- Session scheduling, repeat generation, conflict detection.
- Session notes, assistant assignment, tasks, and status lifecycle.
- Client view of completed session records.
- Reporting cards (day/week/month attended sessions) and recent sessions tables.

Out of scope (v1):
- Billing/payments.
- Notifications (email/in-app reminders).
- Public self-registration.
- Advanced analytics exports.
- Multi-branch / multi-location support.

## 4. Process Flow (End-to-End)
1. Front Desk registers client account/profile.
2. Front Desk creates session:
   - Single-date hourly session, or
   - Daily repeat (up to 30 days) at same hour.
3. System validates:
   - Business hours/day,
   - Therapist/client overlap conflicts.
4. For repeat mode, system creates non-conflicting entries and reports skipped dates.
5. Therapist views assigned pending sessions for today.
6. Therapist updates session with description/notes, assigns assistant, creates one or more tasks.
7. Assistant views today’s assigned sessions and updates task statuses (`pending`/`completed`).
8. Therapist (or Admin) marks session `completed` once requirements are met.
9. Completed session becomes visible to client dashboard/details.
10. Dashboards update attended counts based on `completed + has tasks`.

## 5. Public Interfaces / Types / Data Contracts

### 5.1 Database schema (domain)
`users`
- `id`
- `first_name`, `middle_name` nullable, `last_name`
- `address`
- `contact_no`
- `email` unique
- `gender` enum: `male`, `female`
- `role` enum: `admin`, `therapist`, `assistant`, `front_desk`, `client`
- `status` enum: `active`, `inactive`
- auth fields: `password`, `email_verified_at`, `remember_token`
- `must_change_password` boolean default true
- timestamps

`sessions` (domain sessions)
- `id`
- `date` (date)
- `time` (time, hour-slot)
- `type` enum: `initial`, `regular`
- `client_id` FK users
- `therapist_id` FK users
- `assistant_id` FK users nullable
- `description` nullable text
- `notes` nullable longText
- `status` enum: `pending`, `completed`, `cancelled`
- timestamps

`tasks`
- `id`
- `session_id` FK sessions cascade delete
- `name`
- `description` nullable text
- `status` enum: `pending`, `completed`
- timestamps

### 5.2 Critical migration note
Laravel starter includes framework `sessions` table migration for session storage. Because business requires a domain table named `sessions`, use one of these implementation-safe decisions in code:
- Rename framework session table migration to `http_sessions`, or
- Use non-database session driver and remove/replace framework session table migration before first migration run.

Chosen default for implementation: rename framework table to `http_sessions` to preserve flexibility.

### 5.3 Eloquent model relationships
- User:
  - `clientSessions()` hasMany Session via `client_id`
  - `therapistSessions()` hasMany Session via `therapist_id`
  - `assistantSessions()` hasMany Session via `assistant_id`
- Session:
  - `client()` belongsTo User
  - `therapist()` belongsTo User
  - `assistant()` belongsTo User nullable
  - `tasks()` hasMany Task
- Task:
  - `session()` belongsTo Session

### 5.4 Enums / constants
Create PHP enums for:
- `UserRole`, `UserStatus`, `Gender`
- `SessionType`, `SessionStatus`
- `TaskStatus`

## 6. Role-Based Access Matrix

| Capability | Admin | Front Desk | Therapist | Assistant | Client |
|---|---|---|---|---|---|
| View own dashboard | Yes | Yes | Yes | Yes | Yes |
| Manage staff users | Yes | No | No | No | No |
| Manage client users | Optional read-only | Yes (create/edit active clients) | No | No | Self-only |
| Create sessions | Yes (override) | Yes | No | No | No |
| Update pending session schedule fields | Yes | Yes | Limited (non-schedule notes/tasks) | No | No |
| Assign assistant | Yes | No | Yes | No | No |
| Add/edit tasks | Yes | No | Yes | Update status only | No |
| Change session status | Yes | No | Yes | No | No |
| View notes/details | Yes | Yes | Yes | Assigned only | Completed only |

## 7. Functional Requirements by Module

### 7.1 Authentication and account policies
- Breeze auth screens retained.
- Registration route disabled for public users.
- Internal user creation only through role-authorized screens.
- First login enforces password change when `must_change_password=true`.
- Inactive users cannot login.

### 7.2 Admin module
- User management for staff (`therapist`, `assistant`, `front_desk`, optional `admin`).
- Admin dashboard:
  - Cards: attended sessions today/week/month.
  - Recent 50 sessions table with client, therapist, assistant, date, time, status, notes-view action.
- Can override lock on completed sessions for correction.

### 7.3 Front Desk module
- Client management with required demographic/contact fields.
- Scheduling form fields:
  - date, time (hour slot), type (`initial`/`regular`), therapist, description, schedule mode (single/repeat).
- Repeat scheduler:
  - daily recurrence up to 30 generated dates.
  - skip conflicts automatically and return summary list.
- Front Desk dashboard:
  - FullCalendar of scheduled sessions.
  - Recent 50 sessions table.

### 7.4 Therapist module
- Today dashboard: assigned sessions table (date/time/client/assistant/status).
- Session detail page:
  - set/update assistant,
  - add/update description and notes while pending,
  - add one or more tasks.
- Completion constraints:
  - assistant must be assigned,
  - at least one task exists.
- Can move `pending -> completed` or `pending -> cancelled`.

### 7.5 Assistant module
- Today dashboard: sessions where assistant is assigned.
- Session detail:
  - view session/task context,
  - update task statuses only.
- Cannot edit notes, schedule, therapist assignment, or session status.

### 7.6 Client module
- Dashboard lists completed sessions only.
- Session detail shows description, notes, tasks, therapist/assistant/date/time/type.
- No edit actions.

## 8. Validation and Business Rules
- All control structures use Form Request validation per resource action.
- Session creation rules:
  - date/time must fall inside center business schedule.
  - therapist and client must be active users of correct roles.
  - strict overlap checks:
    - no same therapist at same date+time,
    - no same client at same date+time.
- Status transitions:
  - `pending -> completed|cancelled`
  - no direct edits to completed session except admin override workflow.
- Task status allowed values: `pending`, `completed`.
- Unique constraints and indexes:
  - indexes on `sessions(date, time)`, `client_id`, `therapist_id`, `assistant_id`, `status`.
  - composite unique strategy for conflict checks via application rule + indexed lookup.

## 9. UI/UX Requirements
- Tailwind UI aligned with Breeze style system.
- Data tables:
  - server-side pagination, default sort by newest date/time desc, search by client/therapist.
- Calendar:
  - FullCalendar month/week/day modes.
  - click event opens session detail.
  - color coding by status (`pending`, `completed`, `cancelled`).
- Clear badge system for statuses across all dashboards.
- Completed sessions visually locked.

## 10. Laravel 12 Technical Architecture
- Controllers: role-based resource controllers under `App\Http\Controllers\{Admin,FrontDesk,Therapist,Assistant,Client}`.
- Form Requests: dedicated request classes for every create/update action.
- Policies/Gates:
  - `SessionPolicy`, `TaskPolicy`, `UserPolicy`.
- Services:
  - `SessionSchedulerService` (single/repeat generation + conflict handling),
  - `AttendanceMetricsService` (attended day/week/month counts),
  - `SessionLockService` (completed-state lock and admin override).
- Middleware:
  - `EnsureRole` middleware registered in `bootstrap/app.php`.
- Query patterns:
  - eager loading for dashboards (`with(['client','therapist','assistant'])`) to avoid N+1.
- Transactions:
  - session + task write flows wrapped in DB transactions.

## 11. Reporting Definitions
- `attended_session` formula:
  - `sessions.status = completed` AND `exists(tasks where tasks.session_id = sessions.id)`.
- Day/week/month cards computed in app timezone `Asia/Manila`.
- “Recent 50 sessions” includes all statuses, default newest first.

## 12. Testing Strategy (Pest 4)
Feature tests:
- Auth and role access restrictions per route.
- Admin staff CRUD permission boundaries.
- Front Desk client registration validation.
- Single scheduling with valid/invalid hours.
- Conflict prevention for therapist/client overlap.
- Repeat scheduler creates expected count and skips conflicts.
- Therapist cannot complete session without assistant/tasks.
- Therapist/Admin can complete/cancel; others cannot.
- Assistant can update task status only.
- Completed session lock enforcement and admin override.
- Client can only view completed sessions.
- Dashboard metrics count only attended formula.

Unit tests:
- Enum casting and state transition helpers.
- Scheduler service recurrence logic.
- Attendance metrics service date bucket correctness (day/week/month in Asia/Manila).

## 13. Acceptance Criteria
- Each role sees only its dashboard and allowed actions.
- Front Desk can schedule single and repeat sessions with conflict-safe behavior.
- Therapist workflow supports notes/tasks and controlled completion.
- Assistant can execute task updates without privilege escalation.
- Client sees completed sessions and notes only.
- Admin dashboard metrics and recent sessions table match formula and filters.
- All critical workflows covered by passing Pest tests.

## 14. Implementation Milestones
1. Foundation:
   - schema, enums, models, relationships, seed baseline roles/users.
2. Access control:
   - middleware/policies/role route groups.
3. Front Desk + scheduling engine:
   - client CRUD + calendar + scheduler service.
4. Therapist + tasks:
   - session detail, assistant assignment, tasks, completion flow.
5. Assistant + client portals:
   - task update UI and client read-only session pages.
6. Dashboards + metrics + hardening:
   - counts/tables, authorization audits, test completion.

## 15. Assumptions and Defaults
- Monolithic Laravel app (no separate API/SPA in v1).
- No notification subsystem in v1.
- No cancellation reason field in v1 (can be added in v2).
- Sessions are hourly slots; duration is fixed at 1 hour.
- All center users operate in Asia/Manila timezone.
- Completed records are immutable except explicit Admin override action.

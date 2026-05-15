# Ticketing System

A Laravel system for storing, reviewing, and sending tickets to an external API for approval, along with an internal notification system and email notifications.

---

## Features

- **Ticketing System**: Includes ticket creation and deletion by the user
- **Ticket Management**: Includes initial approval or rejection of tickets by a level-one admin, and approval with API submission or rejection by a level-two admin
- **Authentication**: Login is possible using a password. Registration is simple and available only with email and password
- **Panel**: The panel system is implemented using Vue.js, and user access levels with mixed roles on tickets are managed using Policies
- **SOLID Principles**: Clean architecture with service layer and state

---


## Requirements
- PHP ^8.3
- Laravel ^13.7
- Node ^22.22.7
- NPM ^10.9
- Vue ^3.5.34
- MySQL
- Composer

---

## Installation

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

php artisan serve
# optional: run on custom port for fake API
php artisan serve --port=2000

npm run dev
php artisan queue:work
php artisan schedule:work
```

## Architecture & Project Structure

The project is built using a **clean layered architecture** to ensure separation of concerns and scalability.

### Application Layers

- **Controllers**
    - Handle HTTP requests and responses
    - Keep logic minimal
    - Delegate business logic to services

- **Services**
    - Core ticket workflow logic implemented via **State Pattern**
    - Approval/rejection processing handled through state-based transitions and centralized `change()` method
    - External API integration executed in transactional flow (Level 2 approval)
    - File handling delegated to dedicated **FileUploadService**

- **Infrastructure (Adapters)**
    - Implements an **Adapter Pattern** to integrate external ticket confirmation API
    - Handles API response normalization into a consistent internal format (`status`, `successful`, `body`)
    - Implements failure handling with graceful fallback (`RequestException`)
    - Persists external interaction logs into `TicketLog` for traceability and audit

- **Repositories**
    - Encapsulate database query logic and role-based data access rules (`getList` method with conditional filters per user role)
    - Isolate Eloquent operations from services and controllers
    - Apply role-based visibility rules directly at query level (User, LevelOne, LevelTwo access scopes) 
    - Manage file lifecycle integration through `FileUploadService` during create/delete flows

- **Models**
    - Domain layer entities defining structure and relationships (`User`, `Ticket`, `TicketLog`)
    - Use of **Enum casting** for strict domain states (`TicketStatus`, `UserRole`)
    - Relationship mapping: `User → Tickets → Logs`
    - Encapsulation of sensitive attributes and controlled exposure
    - Ticket model : 
      * Integration with **State Pattern** via `state()` method
      * **Route model binding override** using `code` instead of `id`
      * Policy-based authorization attached at model level
      * Lifecycle hooks for domain rules (auto-generation of ticket `code`)

- **Policies**
    - Manage authorization rules for different user roles

- **Events & Listeners**
    - Implements an **event-driven architecture** for ticket status changes (`TicketStatusChanged`)
    - Decouples domain logic from side effects using **event → listener → action pipeline**
    - Listener (`SendTicketStatusChangedNotification`) delegates processing to an **Action class**
    - Action (`SendTicketStatusNotification`) handles notification dispatch to the ticket owner
    - Uses Laravel **Notification system** with both `mail` and `database` channels
    - Notifications are **queued (ShouldQueue)** for asynchronous processing
    - Ensures separation of concerns between:
        - Domain event (state change)
        - Application logic (action)
        - Infrastructure layer (email + database notifications)

- **Queues**
    - Handle asynchronous processing of ticket re-confirmation logic via `ConfirmFailedTicketJob`
    - Process tickets with `Send` status after a delay (retry mechanism via scheduled command)
    - Use `ShouldQueue` jobs to decouple long-running operations from HTTP lifecycle, especially for notifications and external API retries
---

### Frontend Architecture (Vue.js)

- Uses **Vue 3 Composition API** with local reactive state (`ref`) for managing UI state (tickets, modals, forms, notifications)
- Centralized API communication via a custom wrapper (`myFetch`) handling all HTTP requests and authentication token injection
- Component logic is page-based with modular UI sections (tickets table, detail modal, create/reject modals, notifications modal)
- Role-based UI rendering using `user.role.key` to control access to actions (create, approve, reject, delete)
- Separate pagination handling for tickets and notifications with independent state management
- Form handling via `FormData` with basic client-side validation before API submission
- Modal system managed through boolean state flags with a shared `closeModal()` reset handler
---

## API Endpoints

The API is organized into public and authenticated sections. Authentication is handled via **Laravel Sanctum (Bearer Token)**.

---

### Public Endpoints (No Authentication Required)
These endpoints are accessible without authentication and are used for user onboarding and testing flows.

- `POST /api/auth/login`  
  Authenticates a user using email and password and returns a Sanctum access token.
- `POST /api/auth/register`  
  Registers a new user account with basic credentials.
- `POST /api/endpoint/fake/confirm-ticket`  
  Mock endpoint used to simulate external API ticket confirmation responses.

---

### Authenticated Endpoints (Bearer Token Required)

These endpoints require a valid Sanctum Bearer token and are scoped under the dashboard domain.

#### Ticket Management
- `GET /api/dashboard/tickets`  
  Retrieves a paginated list of tickets based on user role and permissions.
- `POST /api/dashboard/tickets`  
  Creates a new ticket with optional file attachment.
- `DELETE /api/dashboard/tickets/{ticket}`  
  Deletes a ticket (allowed only for owner or permitted roles).
- `PATCH /api/dashboard/tickets/{ticket}/rejected`  
  Rejects a ticket with an optional status message (Level 1 or Level 2 workflow).
- `PATCH /api/dashboard/tickets/{ticket}/accepted`  
  Approves a ticket. In Level 2 flow, triggers external API submission after approval.

---

#### Notifications

- `GET /api/dashboard/profile/notifications`  
  Returns a paginated list of user notifications related to ticket status changes and system events.
---- 
## Default Admin Specifications

### Level One Admin
- `email: level_one@gmail.com`
- `password: password`

### Level Two Admin
- `email: level_two@gmail.com`
- `password: password`

# HPWays Immigration Application Management System

## Project Architecture

**HPWays** is a Laravel-based immigration application management system supporting the full lifecycle from eligibility screening to document processing. The system uses role-based access control (Spatie/Laravel-Permission) with four main user types: applicants, case managers, attorneys, and printing department staff.

### Core Domain Models
- **Application**: Central entity tracking visa applications with `visa_type` (I90, I130, I485, I751, K1, DACA, N400), status, and assignment relationships
- **User**: Enhanced with roles and relationships to applications (as applicant, case_manager_id, attorney_id, assigned_printer_id)  
- **Package**: Service offerings linked to visa types with pricing in cents and feature arrays
- **Document**: File attachments with translation tracking and required document linkage
- **QuizNode**: Graph-based eligibility questionnaire with terminal outcomes mapped to visa types

### Key Architectural Patterns

#### Quiz-Driven Application Flow
The system starts with an eligibility quiz (`QuizController`) that guides users through a decision tree. Terminal outcomes map to actionable visa types via `VisaTypeMapper` service and `config/quiz.php`. Quiz state is managed in session with keys like `quiz.current`, `quiz.history`, and `quiz.visa_type`.

#### Service Layer Pattern
Critical business logic is centralized in `/app/Services/`:
- `VisaTypeMapper`: Maps quiz terminals to internal visa codes using `config/quiz.php` mappings
- `QuizGraphService`: Builds normalized quiz specifications from database
- `ApplicationStatusService`: Manages application state transitions with validation rules
- `PaymentStateMachine`: Handles payment workflow logic
- `RequiredDocumentService`: Determines missing documents per visa type and package tier

#### Multi-Role Dashboard Architecture
Role-specific controllers handle different user workflows:
- `ApplicantController`: Application submission and tracking
- `CaseManagerController`: Case assignment and document review  
- `AttorneyController`: Legal review and approval workflows
- `PrintingDepartmentController`: Document printing and shipping

### Development Workflows

#### Local Development
```bash
# Start development servers (Laravel + Vite HMR)
./start-server.sh
# Access: http://localhost:8081 (Laravel), http://localhost:5173 (Vite)
```

#### Database Operations
```bash
php artisan migrate       # Run migrations
php artisan db:seed      # Seed test data
php artisan tinker       # Interactive REPL
```

#### Asset Building
```bash
npm run dev    # Development with HMR
npm run build  # Production build
```

### Project-Specific Conventions

#### Model Relationships
- Applications use `selected_package_id` to link to packages, not direct `package_id`
- User roles are managed via Spatie permissions, check with `$user->hasRole('attorney')`
- JSON fields like `missing_documents` and `intake_history` are cast to arrays automatically

#### Configuration-Driven Logic
- Visa types defined in `config/visas.php` - modify here, not hardcoded
- Quiz terminals and mappings in `config/quiz.php` - single source of truth for terminal codes, actionable terminals, and terminal-to-visa-type mappings
- Required documents per visa type in `config/required_documents.php` with translation flags

#### File Organization  
- **Hybrid Architecture**: Mix of traditional Laravel structure with direct PHP files in `/public/` for specific user interfaces (attorney-review-case.php, attorney-dashboard.php, case-manager.html)
- **Enum Usage**: Payment status uses PHP 8.1 enums (`App\Enums\PaymentStatus`)
- **Service Integration**: External services like Stripe payments integrated via dedicated controllers
- **Direct PHP Bootstrapping**: Files in `public/` manually bootstrap Laravel via `require __DIR__ . '/../bootstrap/app.php'`

#### State Management Patterns
- Quiz progress stored in session with `quiz.current` (node/terminal), `quiz.history` (navigation path), `quiz.visa_type` (resolved type)
- Applications use draft reuse pattern - existing draft applications for same visa type are updated rather than creating duplicates
- Status transitions managed by `ApplicationStatusService` with validation rules between states

#### Development Notes
- Uses SQLite for development (`database/database.sqlite`)
- Vite integration for frontend asset pipeline
- Spatie Laravel-Permission for role-based access control
- Laravel Sanctum for API authentication

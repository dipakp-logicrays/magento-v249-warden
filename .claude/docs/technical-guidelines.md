# Adobe Commerce Technical Guidelines

**Reference this file for Magento 2 architecture, design patterns, and technical implementation guidelines.**

---

## Basic Programming Principles

- **Function arguments MUST NOT** be modified
- **Explicit return types MUST** be declared on functions
- **Type hints for scalar arguments SHOULD** be used
- **All new PHP files MUST** have strict type mode enabled by starting with `declare(strict_types=1);`
- **All updated PHP files SHOULD** have strict type mode enabled

---

## Class Design Standards

- **Object decomposition MUST** follow SOLID principles
- **Object MUST** be ready for use after instantiation - no additional public initialization methods allowed
- **Factories SHOULD** be used for object instantiation instead of `new` keyword
- **Class constructor** can have only dependency assignment and/or argument validation operations
- **Constructor SHOULD** throw exception when validation of argument has failed
- **Events MUST NOT** be triggered in constructors
- **All dependencies MUST** be requested by the most generic type required
- **Proxies and interceptors MUST NEVER** be explicitly requested in constructors
- **Inheritance SHOULD NOT** be used - composition SHOULD be used for code reuse
- **All non-public properties and methods SHOULD** be private
- **Abstract classes MUST NOT** be marked as public `@api`
- **Service classes SHOULD NOT** have mutable state
- **Only data objects or entities MAY** have observable state
- **"Setters" SHOULD NOT** be used (only allowed in DTOs)
- **"Getters" SHOULD NOT** change object state
- **Static methods SHOULD NOT** be used
- **Temporal coupling MUST** be avoided
- **Method chaining MUST** be avoided
- **Law of Demeter SHOULD** be obeyed

---

## Dependency Injection

- **There SHOULD** be no circular dependencies between objects
- **app/etc/di.xml MUST** contain only framework-level DI settings
- **All modular DI settings SHOULD** be stored in `<module_dir>/etc/di.xml`
- **All modular Presentation layer DI settings SHOULD** be stored in `<module_dir>/etc/<area_code>/di.xml`

---

## Interception Standards

- **Around-plugins SHOULD** only be used when behavior substitution is needed
- **Plugins SHOULD NOT** be used within own module
- **Plugins SHOULD NOT** be added to data objects
- **Plugins MUST** be stateless
- **Plugins SHOULD NOT** change state of intercepted object

---

## Exception Handling Standards

- **All exceptions MUST** produce error messages with: Symptom, Details, Solution/workaround
- **Exceptions MUST NOT** be handled in same function where thrown
- **Exceptions MUST NOT** handle message output
- **Business logic MUST NOT** be managed with exceptions
- **Exception class short name MUST** be clear and meaningful
- **Thrown exceptions SHOULD** be as specific as possible
- **All third-party library communications MUST** be wrapped with try/catch
- **`\Exception` SHOULD** be caught only in code calling third-party libraries
- **`\Exception` SHOULD NOT** be thrown in Controllers
- **Separate exceptions hierarchy SHOULD** be defined on each application layer
- **It is NOT allowed** to absorb exceptions with no logging
- **Exceptions SHOULD NOT** be caught in loops
- **Methods using system resources MUST** use try/finally blocks
- **Exceptions displayed to users MUST** be sub-types of `LocalizedException`

---

## Application Layer Standards

### All Layers
- **Application SHOULD** be structured in compliance with CQRS principle
- **Every layer MUST** process exceptions of underlying layer
- **A layer MUST NOT** depend on layer above it

### Presentation Layer
- **Request, Response, Session objects MUST** be used only in Presentation layer
- **All actions MUST** return `ResultInterface` implementation
- **Actions MUST NOT** reference blocks declared in layout
- **Blocks MUST NOT** assume specific controller was invoked
- **Templates MUST NOT** instantiate objects

### Data Access Layer
- **Every persistence operation MUST** be performed with one scope set
- **Entities MUST NOT** contain persistence-related logic
- **MySQL strict_mode SHOULD** be aligned with latest MySQL release default

### Service Contracts Layer
- **Service contract interfaces SHOULD** be placed in separate API modules
- **Service interfaces for web APIs MUST** be placed under `MyModuleApi/Api` directory
- **Service data interfaces MUST** be placed under `MyModuleApi/Api/Data`
- **Each service interface SHOULD** declare single public method
- **Service contracts SHOULD** support batch data processing
- **Batch retrieval operations MUST** accept `SearchCriteriaInterface`
- **Service data interfaces SHOULD** extend `ExtensibleDataInterface`
- **Services SHOULD NOT** apply ACL rules

---

## Module Development Standards

### Module Structure
- **Follow standard directory structure**
- **Use proper namespace conventions**
- **Implement dependency injection** properly
- **Declare all dependencies** in `module.xml`
- **Use semantic versioning** for module versions

### Module Registration
- **Include `registration.php`** in module root
- **Use proper module name** format: `Vendor_Module`
- **Register in correct sequence**

### Module Dependencies
- **Declare ALL module dependencies** in `module.xml`
- **Use sequence** for load order dependencies
- **Avoid circular dependencies**
- **Declare soft dependencies** when appropriate
- **Document third-party dependencies**

### Configuration Files
- **Use XML schema validation**
- **Follow Magento configuration** standards
- **Use proper config scopes** (global, adminhtml, frontend)
- **Merge configs properly** using inheritance
- **Validate XML** before deployment

### Database Schema
- **Use declarative schema** (`db_schema.xml`)
- **Don't use setup scripts** for schema (deprecated in 2.3+)
- **Use proper column types** and attributes
- **Define foreign keys** properly
- **Create indexes** for frequently queried columns
- **Use `db_schema_whitelist.json`** for validation

### Data Patches
- **Use Data Patches** for data modifications
- **Implement proper dependencies** between patches
- **Make patches revertible** when possible
- **Use patches for EAV attributes**
- **Don't modify data** in schema patches

### Events & Observers
- **Use events** for loose coupling
- **Name events descriptively**
- **Document event parameters**
- **Keep observers lightweight**
- **Don't use events** for required functionality
- **Prefer plugins** for interception

### Plugins (Interceptors)
- **Use plugins** for extending functionality
- **Prefer after/before** over around plugins
- **Don't use plugins** on other plugins
- **Keep plugins stateless**
- **Document plugin behavior**
- **Test plugin compatibility**

### Layouts & Templates
- **Use layout XML** for page structure
- **Keep templates logic-free**
- **Use view models** instead of blocks for complex logic
- **Follow template hierarchy**
- **Don't override** core templates unnecessarily
- **Use layout handles** appropriately

### UI Components
- **Use UI Components** for complex admin grids/forms
- **Define components** in XML
- **Use data providers** for component data
- **Extend components** properly
- **Follow UI Component** best practices

### Web API
- **Define service contracts** for APIs
- **Use proper HTTP methods** (GET, POST, PUT, DELETE)
- **Implement proper authentication**
- **Document API endpoints**
- **Version APIs** for changes
- **Use `webapi.xml`** for REST routing

### Message Queue
- **Use message queues** for async operations
- **Define queue topology** properly
- **Implement consumers** correctly
- **Handle message failures**
- **Use proper queue types** (db, amqp, etc.)

### Cron Jobs
- **Use cron** for scheduled tasks
- **Define cron schedule** in `crontab.xml`
- **Keep cron jobs efficient**
- **Handle cron failures** gracefully
- **Log cron execution**
- **Don't use cron** for time-sensitive operations

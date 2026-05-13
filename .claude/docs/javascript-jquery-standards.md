# JavaScript and jQuery Coding Standards

**Reference this file when working with JavaScript, jQuery widgets, or frontend JavaScript code.**

---

## JavaScript Coding Standards

### Formatting Standards
- **Use 4 spaces** for indentation
- **Add single linefeed** at end of file
- **Max line length** is 120 characters
- **Use UNIX line termination** (LF)
- **Use string concatenation** for multi-line literals
- **Use braces** with all multiline blocks
- **Always use semicolons** as statement terminators
- **Use single quotes** for strings

### Naming Conventions
- **Avoid underscores and numbers** in names
- **Use accurate descriptive names** for variables/methods
- **Private/protected methods** start with underscore
- **Function names** start with English verb
- **Use `get`/`set` prefix** for accessors
- **Use `has`/`is` prefix** for Boolean methods

### Additional Standards
- **Put operators** on preceding line
- **Use semantic HTML markup** only
- **Use standard features** for portability
- **Avoid closures** attached to DOM elements
- **Use explicit scope** for clarity
- **Don't modify built-in objects**
- **Declare variables with `var`**

---

## JavaScript DocBlock Standards

### JSDoc Requirements
- **Document all files, classes, methods** with JSDoc
- **Use `//** for inline comments
- **Begin JSDoc** with `/**`
- **Enclose inline tags** in braces: `{@code this}`
- **Start block tags** on own line

### JSDoc Tags
- **`@const`** - marks variable read-only
- **`@extends`** - indicates class inheritance
- **`@interface`** - indicates function defines interface
- **`@implements`** - indicates class implements interface
- **`@override`** - indicates method/property override
- **`@param`** - documents function arguments
- **`@return`** - documents return type
- **`@type`** - identifies variable/property type

---

## jQuery Widget Coding Standards

### Naming Conventions
- **Widget names MUST** be camel case English words
- **Widget names SHOULD** be verbose enough to describe purpose

### Instantiation Standards
- **Load additional JS files** using `$.mage.components()`
- **Use `$.mage.extend()`** to extend widget resources
- **Instantiate widgets** using `data-mage-init` attribute
- **Use `.mage()` plugin** for widgets with callbacks

### Development Standards
- **Widgets SHOULD** comply with single responsibility principle
- **Widget properties MUST** be in options for configurability
- **Widget communications MUST** be handled by jQuery events
- **Use DOM event bubbling** for parent-child communication
- **Comply with Law of Demeter** principle
- **Don't instantiate widgets** inside other widgets

### Architecture Standards
- **Use underscore prefix** for private methods
- **Start element selection** with `this.element`
- **Widget options SHOULD** have default values
- **Pass DOM selectors** as widget options
- **Use `_setOption` method** for state changes
- **Handle widget initialization** for successive calls
- **Clean up on destruction** to original state
- **Bind events** using `_bind()` method

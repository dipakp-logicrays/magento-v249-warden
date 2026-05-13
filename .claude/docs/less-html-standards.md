# LESS and HTML Coding Standards

**Reference this file when working with LESS/CSS styling or HTML templates.**

---

## LESS Coding Standards

### General Rules
- **Use 4 spaces** for indentation
- **Add space before** opening braces and line break after
- **Add line break** after each selector delimiter
- **Use single quotes** for strings
- **Add spaces** before and after combinators
- **Start each property** in new line
- **Add space after** but not before colon
- **Add blank line** at end of file and after selectors
- **Add semicolon** after property
- **Avoid `!important`** if possible

### Selectors
- **Avoid `id` selectors**
- **Class names SHOULD** be lowercase, start with letter
- **Separate words** with dash '-'
- **Helper classes** start with underscore '_'
- **Use short but descriptive** class names
- **Use meaningful, specific** class names
- **Avoid qualifying** class names with type selectors
- **Type selectors MUST** be lowercase
- **Write selectors** in one line
- **Avoid more than 3 levels** of nesting

### Properties
- **Sort properties** alphabetically
- **Use shorthand properties** where possible
- **Don't specify units** for "0" values
- **Omit leading "0"s** in values
- **Use lowercase** hexadecimal notation
- **Use 3-character hex** where possible
- **Avoid hex values** in properties - use variables

### Variables
- **Local variables** in module file beginning
- **Theme variables** in `_theme.less` file
- **All variable names MUST** be lowercase
- **Value variables**: `@property-name`
- **Parameter variables**: `@component-element__state__property__modifier`

### Mixins
- **Theme mixins** in `web/css/source` directory
- **Apply class naming rules** for mixins
- **Use double underscore** prefix for grouping
- **Set default values** for parameters

---

## HTML Coding Standards

### Basic Formatting
- **Use 4 spaces** for indentation
- **Add blank line** at end of file
- **Always close** self-closing tags
- **Avoid lines longer** than 120 characters
- **Align tag attributes** one under another for readability

### Syntax Standards
- **No spaces around** equals sign (recommended)
- **Use one space after** colon in attributes
- **Use appropriate HTML5** elements for blocks
- **Use semantic class names** and IDs
- **Avoid presentational** class names

### Accessibility & Standards
- **Comply with WCAG 2.0** guidelines
- **Include microdata** on crucial pages

---

## Code Demarcation Standards

### Semantics
- **Use meaningful lowercase words** with hyphens for attributes
- **Semantic representation** may rely on ID attribute
- **Follow separation** of presentation and content
- **Use semantic HTML markup** only

### Visual Representation Patterns
- **Visual representation MUST** rely only on HTML class attributes
- **Use HTML class attributes** as first option
- **Don't hard-code CSS styles** in JavaScript
- **Don't use inline CSS styles** in HTML tags

### Business Logic
- **Business logic MUST** rely on form/data attributes only
- **Assign HTML helper classes** in JavaScript
- **Helper class names** start with underscore and lowercase
- **Don't select DOM elements** based on HTML structure
- **Use jQuery templates** for recurring markup

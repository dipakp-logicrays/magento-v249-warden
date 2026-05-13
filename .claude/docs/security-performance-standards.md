# Security and Performance Standards

**Reference this file when working on security-related features, performance optimization, or testing.**

---

## Security Standards

### Input Validation & Sanitization
- **Validate ALL user input** before processing
- **Use whitelisting** over blacklisting for input validation
- **Sanitize data** before storage and output
- **Never trust client-side validation** - always validate server-side
- **Use Magento validators** from `Magento\Framework\Validator`

### Output Escaping
- **Escape ALL output** in templates using appropriate methods:
  - `$escaper->escapeHtml()` - For HTML content
  - `$escaper->escapeHtmlAttr()` - For HTML attributes
  - `$escaper->escapeUrl()` - For URLs
  - `$escaper->escapeJs()` - For JavaScript strings
  - `$escaper->escapeCss()` - For CSS
- **NEVER output unescaped user input**
- **Use proper context-aware escaping**

### SQL Injection Prevention
- **ALWAYS use prepared statements** with parameter binding
- **NEVER concatenate user input** into SQL queries
- **Use Magento query builders** and collections
- **Avoid raw SQL** when possible
- **Use `addFieldToFilter()`** for collection filtering

### Cross-Site Scripting (XSS) Prevention
- **Escape output** in all templates
- **Validate and sanitize** rich text content
- **Use Content Security Policy** headers
- **Sanitize user-generated content** before storage
- **Don't use `innerHTML`** with user content

### Cross-Site Request Forgery (CSRF) Prevention
- **Use form keys** for all POST requests
- **Validate form keys** in controllers
- **Include `formkey` in AJAX requests**
- **Use `\Magento\Framework\Data\Form\FormKey` for generation**

### Authentication & Authorization
- **Implement proper ACL** for admin resources
- **Use Magento authentication** mechanisms
- **Never store passwords in plain text**
- **Use proper password hashing** (bcrypt)
- **Implement rate limiting** for login attempts
- **Validate user permissions** before actions

### Session Management
- **Use Magento session** management
- **Regenerate session IDs** after login
- **Set proper session timeouts**
- **Use secure and httponly flags** for cookies
- **Don't store sensitive data** in sessions

### File Upload Security
- **Validate file types** using MIME and extension
- **Limit file sizes**
- **Store uploads outside** web root when possible
- **Scan for malware** if applicable
- **Use unique filenames** to prevent overwrites
- **Validate image files** using proper libraries

### API Security
- **Use authentication tokens** for REST/SOAP APIs
- **Implement rate limiting**
- **Validate API inputs** strictly
- **Use HTTPS** for API endpoints
- **Implement proper CORS** policies
- **Version APIs** for backward compatibility

### Sensitive Data Handling
- **Encrypt sensitive data** at rest
- **Use HTTPS** for data in transit
- **Don't log sensitive information** (passwords, credit cards, etc.)
- **Use Magento Vault** for payment information
- **Implement data retention** policies
- **Follow PCI DSS** for payment data

### Error Handling & Logging
- **Don't expose stack traces** to users
- **Log security events** appropriately
- **Use generic error messages** for users
- **Log with proper severity levels**
- **Don't log sensitive data**
- **Monitor logs** for suspicious activity

### Configuration Security
- **Store sensitive config** in `env.php`
- **Use encrypted config** for credentials
- **Don't commit credentials** to version control
- **Use environment variables** for secrets
- **Implement proper file permissions**
- **Secure admin panel URL**

---

## Performance Standards

### Database Optimization
- **Use collections** efficiently
- **Add proper indexes**
- **Avoid N+1 queries**
- **Use `addFieldToSelect()`** to limit columns
- **Implement pagination** for large datasets
- **Use flat tables** for catalog

### Caching
- **Implement Full Page Cache** compatibility
- **Use cache tags** appropriately
- **Clear specific cache** types when needed
- **Use Varnish** for production
- **Implement ESI** for dynamic blocks
- **Cache expensive operations**

### Code Optimization
- **Avoid loading** unnecessary data
- **Use lazy loading** when appropriate
- **Minimize object creation**
- **Use factories** appropriately
- **Avoid circular references**
- **Profile and optimize** bottlenecks

### Frontend Performance
- **Minimize JavaScript**
- **Optimize images**
- **Use CSS sprites** when appropriate
- **Implement lazy loading** for images
- **Minimize HTTP requests**
- **Use CDN** for static assets
- **Enable JavaScript bundling**
- **Minimize and merge CSS/JS**

### Asset Loading
- **Use RequireJS** for JavaScript dependencies
- **Load assets asynchronously** when possible
- **Use proper RequireJS** configurations
- **Minimize critical path** resources

---

## Responsive Design Standards

### Mobile-First Approach
- **Design mobile-first**
- **Use responsive breakpoints**
- **Test on multiple devices**
- **Optimize for touch** interfaces
- **Ensure readability** on small screens

### Media Queries
- **Use Magento breakpoints**
- **Define breakpoints** in LESS variables
- **Test at all breakpoints**

---

## Documentation Standards

### Code Documentation
- **Document all public APIs**
- **Include usage examples**
- **Document complex logic**
- **Keep docs up-to-date**
- **Use proper DocBlock** format

### Module Documentation
- **Include README.md** in module
- **Document configuration** options
- **Provide installation** instructions
- **Document dependencies**
- **Include changelog**

### Technical Documentation
- **Document architecture** decisions
- **Create sequence diagrams** for complex flows
- **Document custom events**
- **Document extension points**

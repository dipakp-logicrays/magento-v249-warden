# PHP Coding Standards

**Reference this file when working with PHP code, PHPDoc blocks, or PHP-related questions.**

---

## PHP Development Standards

### Code Formatting Standards
- **Follow PSR-2 standards** with Magento extensions for all PHP Code
- **Use CamelCase** for class names, **camelCase** for methods and variables
- **Use short array syntax** `[]` instead of `array()`
- **Order imports alphabetically** and group by namespace
- **For dependencies**, favor constructor injection, use type hints
- **Handle exceptions properly** - never suppress or swallow them
- **Use PHP 8.1+ features** where possible
- **Always use constructor property promotion**
- **Always declare parameter types and return types** in methods
- **Don't add return types** to functions that need to abide by an interface (models, resource models, collections, etc.)
- **Always add proper type casting for integer returns** in model classes:
  - For nullable integer fields: `return $this->getData(FIELD) ? (int) $this->getData(FIELD) : null;`
  - For required integer fields: `return (int) $this->getData(FIELD);`
- **Add line break** to the end of files
- **Add @throws tags** to docblocks for exceptions, where appropriate
- **Use trailing commas** for all arrays and method arguments
- **Import all classes** with use statements rather than using FQCNs
- **Do not add copyright headers**, but add line break and `declare(strict_types=1)` to top of each PHP class
- **Don't nest arrays on single line** - always use multi-line format for readability
- **Always add extra line breaks** between conditions and before return statements, unless only statement in block
- **Always add an extra line break** after `parent::__construct` calls
- **Methods that contain one or no parameters** break curly brackets onto a new line
- **Methods with multiple parameters** should have parameters on new lines with trailing commas

---

## DocBlock Standards

### General Principles
- **Be as short as possible** while including all necessary information
- **Follow phpDocumentor standard** formatting
- **Make code self-explanatory** with descriptive names
- **All PHP classes MUST** follow Magento coding standard PHPDocBlock format
- **Use proper indentation** and spacing in DocBlocks
- **Include line breaks** between different sections of DocBlocks

### File-Level DocBlocks
- **Each source file MUST** have DocBlock header with short description
- **Separate descriptions** with empty lines
- **Include @package tag** for module identification
- **Include @author tag** when appropriate
- **Include @since tag** for version tracking

### Class and Interface DocBlocks
- **Classes and Interfaces MUST** have comprehensive DocBlock with:
  - Short description (one line)
  - Long description (if needed)
  - `@api` tag **ONLY** for public API classes (interfaces, service contracts, data interfaces)
  - `@since` tag **ONLY** when introducing new functionality or significant changes
  - `@package` tag **ONLY** for module identification when needed
- **Use short form names** to encourage readability
- **Add use cases** where appropriate

### Property/Attribute DocBlocks
- **All class properties MUST** have DocBlock with `@var` tag
- **Include type declaration** using `@var` tag
- **Include description** for complex properties
- **Use proper type hints** (e.g., `@var string`, `@var \Magento\Framework\ObjectManagerInterface`)
- **For arrays, specify content type** (e.g., `@var string[]`, `@var \Magento\Catalog\Model\Product[]`)

### Constructor DocBlocks
- **All constructors MUST** have DocBlock with:
  - Short description of what the constructor does
  - `@param` tags for each parameter with type and description
  - `@throws` tags for possible exceptions
  - `@api` tag **ONLY** if constructor is part of public API
- **Include parameter descriptions** explaining purpose and constraints
- **Document dependency injection** parameters clearly

### Method and Function DocBlocks
- **All public and protected methods MUST** have DocBlock with:
  - Short description (one line)
  - Long description (if needed)
  - `@param` tags for each parameter with type and description
  - `@return` tag with return type and description
  - `@throws` tags for possible exceptions
  - `@api` tag **ONLY** for public API methods (service contracts, interfaces)
  - `@since` tag **ONLY** when introducing new functionality or significant changes
- **Include meaningful information** beyond method name
- **Use proper type hints** for parameters and return values
- **Document side effects** and state changes
- **Include usage examples** for complex methods

### Constants DocBlocks
- **Constants MUST** have DocBlock with:
  - Short description explaining purpose
  - `@var` tag with type information
  - `@since` tag **ONLY** when introducing new functionality
- **Include value examples** when helpful

### When to Use Specific Tags

#### `@api` Tag Usage
- **Use ONLY for:**
  - Public API interfaces and classes
  - Service contracts (RepositoryInterface, ManagementInterface, etc.)
  - Data interfaces (DataInterface implementations)
  - Public API methods that external modules can depend on
- **DO NOT use for:**
  - Internal model classes
  - Helper classes
  - Internal utility methods
  - Private or protected methods

#### `@since` Tag Usage
- **Use ONLY when:**
  - Introducing new functionality in a module
  - Making significant changes to existing functionality
  - Adding new public API methods
- **DO NOT use for:**
  - Bug fixes
  - Minor internal changes
  - Refactoring without functional changes

#### `@package` Tag Usage
- **Use ONLY when:**
  - Module identification is needed for documentation
  - Working with complex multi-module projects
- **Usually not needed for:**
  - Standard Magento modules
  - Simple custom modules

### DocBlock Tags Reference
- **`@api`** - Mark public API elements (classes, methods, properties)
- **`@deprecated`** - Mark deprecated elements with explanation and replacement
- **`@var`** - Type declaration for properties and inline type hints
- **`@param`** - Document method parameters with type and description
- **`@return`** - Document return type and description
- **`@throws`** - Document possible exceptions with conditions
- **`@since`** - Version when element was introduced
- **`@package`** - Module/package identification
- **`@method`** - Document magic methods
- **`@see`** - Reference related elements
- **`@example`** - Provide usage examples

### PHPDocBlock Examples

#### File-Level DocBlock
```php
<?php
declare(strict_types=1);

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Vendor\Module\Model;
```

#### Class DocBlock
```php
/**
 * Product data model
 *
 * Handles product data operations and business logic
 */
class Product extends \Magento\Framework\Model\AbstractModel
{
```

#### Public API Class DocBlock
```php
/**
 * Product repository interface
 *
 * @api
 * @since 100.0.1
 */
interface ProductRepositoryInterface
{
```

#### Property DocBlock
```php
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var string[]
     */
    private $allowedTypes = ['simple', 'configurable'];
```

#### Constructor DocBlock
```php
    /**
     * Initialize dependencies
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        parent::__construct($data);
    }
```

#### Method DocBlock
```php
    /**
     * Get product by SKU
     *
     * @param string $sku
     * @param bool $editMode
     * @param int|null $storeId
     * @param bool $forceReload
     * @return \Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductBySku(
        string $sku,
        bool $editMode = false,
        ?int $storeId = null,
        bool $forceReload = false
    ): \Magento\Catalog\Api\Data\ProductInterface {
        // Method implementation
    }
```

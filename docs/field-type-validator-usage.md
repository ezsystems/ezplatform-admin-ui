# Field Type Validator

Field Type Validator is a base class to validate inputs in a Field Type.

## How to use it?

```javascript
class Validator extends window.eZ.BaseFieldValidator {
    validateInput(event) {
        // validation

        return {
            isError: {Boolean},
            errorMessage: {String},
        };
    }
};

const validator = new Validator({
    classInvalid: {String},
    fieldSelector: {String},
    eventsMap: [
        {
            selector: {String},
            eventName: {String},
            callback: {String},
            invalidStateSelectors: {Array},
            errorNodeSelectors: {Array},
        },
    ],
});

validator.init();
```

## Class configuration properties

The `BaseFieldValidator` class has a few required properties. All of them are listed below.

**classInvalid** _{String}_ - CSS class name to be added when field is invalid, example: 'is-invalid'.

**fieldSelector** _{String}_ - CSS selector of the field, example: '.ez-field-edit-ezstring',

**eventsMap** _{Array}_  - events config:
- **selector** _{String}_ - CSS selector of input (where to add an event listener), example: '.ez-field-edit-ezstring input'.
- **eventName** _{String}_ - event name, example: 'blur'.
- **callback** _{String}_ - callback for event listener, example: 'validateInput', should return object with two params:
    - **isError** _{Boolean}_ - indicator of error state (true/false).
    - **errorMessage** _{String}_ - text of the error.
- **invalidStateSelectors** {Array} - CSS selectors where to add invalid class, example: ['.ez-field-edit-ezstring'].
- **errorNodeSelectors** {Array} - CSS selectors where to append an error message, example: ['.ez-field-edit-text-zone'].

## Useful methods

**init** - a method for adding event listeners
**reinit** - a method for removing event listeners and adding new ones (useful when DOM has changed)

## Methods to override (optional)

**findValidationStateNodes** - method to find nodes to add invalid class (useful when DOM is complex), this method should return an array of nodes. params:
- **fieldNode** - the parent node (based on **fieldSelector**)
- **input** - the input
- **selectors** - list of CSS selectors (based on **invalidStateSelectors**)

**findErrorContainers** method to find container for the error message (useful when DOM is complex), this method should return an array of nodes. params:
- **fieldNode** - the parent node (based on **fieldSelector**)
- **input** - the input
- **selectors** - list of CSS selectors (based on **errorNodeSelectors**)

**findExistingErrorNodes** method to find existing error nodes (useful when DOM is complex), his method should return an array of nodes. params:
- **fieldNode** - the parent node (based on **fieldSelector**)
- **input** - the input
- **selectors** - list of CSS selectors (based on **errorNodeSelectors** and 'ez-field-error' class)

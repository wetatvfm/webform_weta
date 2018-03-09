# Webform WETA Module

This module extends the Webform module to provide trivia and personality quizzes.

## Dependencies

### Required
**Webform**

### Modules not required by this module but used by WETA for added quiz 
functionality
* Entity Reference
* Field Collection
* Pathauto
* Token
* Webform Default Fields

## Content Types

This modules presupposes that there are two webform-enabled content types:  **quiz_trivia** and **quiz_personality**. 

This module makes no assumptions as to what fields are present on these content types.  In WETA's case, they primarily serve to hold framing information for the quiz and to manage how results are displayed.

### Fields: quiz_trivia
* Title
* Image
* Quiz Description (text area)
* Results Intro (text area)
* Trivia Buckets (field collection)
    * For trivia quizzes, we divide the number of correct answers into "buckets" with snazzy names and witty descriptions. This makes it easier for them to be shared on social platforms. For example, our Doc Martin Trivia Quiz has the following buckets:
      * Surgeon
      * Consultant
      * GP
      * House Officer
      * Med Student
    * The field collections contain the following fields:
      * Quiz Result Title
      * Quiz Result Image
      * Quiz Result Description
      * Minimum Score
        * The minimum score required to achieve this bucket. For example, if the quiz has 10 questions, there might be 4 potential results with minimum scores of:
           * 0 (didn't get any right)
           * 3 (got at least three right)
           * 5 (got at least five right)
           * 8 (got at least eight right)

### Fields: quiz_personality
* Title
* Image
* Quiz Description (text area)
* Results Intro (text area)
* Results (unlimited entity reference)
    * This references an additional content type (quiz_results) that holds the image and description for each potential result. This allows it to be displayed as a an easily cached page that serves as the confirmation page for the webform.
    * Within the webform settings, the confirmation page is set using tokens to incorporate the result of the quiz into the URL. (e.g., [node:url:path]/[submission:values:result]).

#### Fields: quiz_results
* Title
* Image
* Quiz Result Key (text)
    * See Building a Personality Quiz below for documentation on how to construct the key.
* Quiz Result Description (text area)
* Parent Quiz (entity reference)
    * The path alias for the results content type is set to reference the parent quiz and the result. (e.g., [node:field-parent-quiz:url:path]/[node:field-quiz-result-key]).

## Building a Trivia Quiz
1. Create a quiz_trivia node.
2. On the Webform tab, add a Select component for each of your quiz questions. 
    * Webform requires select options to be set up as key-value pairs. Unlike Personality Quizzes, the "safe key" does not have to be specific to the results. However, you should make a note of the "safe key" for the correct answer. 
    * Tick the box labeled "This is a quiz question."  That will mark this component for processing when the quiz is submitted.
    * Enter the "safe key" for the correct answer in the Correct Answer field.
    * [Optional] Add an explanation for the correct answer. For example, a fun fact about the subject of the question, or an explanation as to why the correct answer is the right one.
    * [Optional] I like to tick the box to randomize the options, but it's not required.
3. On the Webform Form Settings sub-tab:
    * Ensure that the confirmation page is set correctly.
    * Under Advanced Settings, uncheck the box "Show the notification about previous submissions."

## Building a Personality Quiz
1. Create a quiz_personality node, and quiz_result nodes for each of the result options.
2. On the Webform tab, add a Select component for each of your quiz questions. 
    * Your question should have the same number of options as there are results. If there are six possible outcomes for the quiz, each question should have six options to choose from.
    * Webform requires select options to be set up as key-value pairs. 
      * The "safe key" for each pair should match the Quiz Result Key for the result that matches that option. For example, for a Downton Abbey personality quiz, the option that matches Anna would have a safe key of "anna" and the Quiz Result Key in the Anna Quiz Result node would be "anna".
      * The "safe key" should be consistent for all the Select components.  All of the options that match Anna should have a safe key of "anna".
    * Tick the box labeled "This is a quiz question."  That will mark this component for processing when the quiz is submitted.
    * [Optional] I like to tick the box to randomize the options, but it's not required.
3. On the Webform Form Settings sub-tab:
    * Ensure that the confirmation page is set correctly.
    * Under Advanced Settings, uncheck the box "Show the notification about previous submissions."
    
## Displaying Results

This module calculates and stores a user's quiz results, but does the bare minimum to display them. That is handled primarily by the content types as mentioned above through view modes and views.  

You will likely need to override webform-confirmation.tpl.php  in your site's theme. I have included WETA's override of this template for reference (and named incorrectly in an attempt to avoid accidental overrides), but depending on how your content types are constructed, it may not work out-of-the-box for you.

(It is also an epic example of what NOT to do in a theme template. If I were to build this again today, I'd move all the logic and processing out of the template file and into a preprocess function.)
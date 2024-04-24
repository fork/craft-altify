# Release Notes for Altify

# [1.0.0-beta.2] (2024-04-24)

### Changes

* The plugin is called Altify from now on

### Features

* Add translation services DeepL, Opus MT and Flan T5 small

# [1.0.0-beta.1] (2024-02-23)

### Features

* Add a blacklist setting for filtering out words like 'arafed'
* Add button for generating an alt text to the asset edit page
* Add an element action for bulk generating alt texts from an asset index page

# [0.1.0] (2023-11-03)

### Features

* Initial Commit
* Set up plugin structure by using Craft Generators
* Implement alt text generators using BLIP model on Hugging Face
* Implement alt text generation service class
* Add queue job for generating alt texts
* Add after save event for assets triggering alt text generation via said queue job
* Add basic plugin settings
* Add basic structure for implementing translation services

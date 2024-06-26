<div align="center">
  <a href="https://www.fork.de">
    <img src="./assets/fork-logo.png" width="156" height="30" alt="Fork Logo" />
  </a>
</div>

# Alt text generator plugin for Craft CMS

Generates alt texts for images using different services that can be chosen from.

## Requirements

This plugin requires Craft CMS 4.5.0 or later, and PHP 8.0.2 or later.

In order to use an alt text generator service, you'll need an api key or other credentials depending on the
authentication method used.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “alt”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require fork/craft-altify

# tell Craft to install the plugin
./craft plugin/install altify
```

## Usage

By default, this plugin uses the [BLIP Model via Hugging Face Inference API](https://huggingface.co/Salesforce/blip-image-captioning-large).
You will need an API Key to be able to use it. It is receommended to set it via ENV variable, but it is also possible to
set it directly in the plugin's settings.

In order to use another model, go to the plugin settings and choose one of those available.
You can also set the model via ENV variable. This can be a class name, or one of the following names.

### Out-of-the-box Available models

<table>
    <tr>
        <th>Model name</th>
        <th>Link</th>
    </tr>
    <tr>
        <td>BLIP (large)</td>
        <td><a>https://huggingface.co/Salesforce/blip-image-captioning-large</a></td>
    </tr>
    <tr>
        <td>BLIP (base)</td>
        <td><a>https://huggingface.co/Salesforce/blip-image-captioning-base</a></td>
    </tr>
    <tr>
        <td>...</td>
        <td>...</td>
    </tr>
</table>

### Out-of-the-box Available translation services

<table>
    <tr>
        <th>Model name</th>
        <th>Link</th>
    </tr>
    <tr>
        <td>DeepL API</td>
        <td><a>https://developers.deepl.com/docs</a></td>
    </tr>
    <tr>
        <td>OPUS MT (EN → DE)</td>
        <td><a>https://huggingface.co/Helsinki-NLP/opus-mt-en-de</a></td>
    </tr>
    <tr>
        <td>Google T5 small (EN → DE)</td>
        <td><a>https://huggingface.co/google-t5/t5-small</a></td>
    </tr>
    <tr>
        <td>...</td>
        <td>...</td>
    </tr>
</table>

### Implementing own alt text generators and translators

You can implement your own alt text generator service by implementing the interface
`fork\alt\connectors\alttextgeneration\AltTextGeneratorInterface` and registering it via the `EVENT_REGISTER_GENERATORS`
event like this:

```
use fork\altify\events\RegisterGeneratorsEvent;
use fork\altify\services\Generator;

Event::on(
    Generator::class,
    Generator::EVENT_REGISTER_GENERATORS,
    function (RegisterGeneratorsEvent $event) {
        $event->generators['myGenerator'] = MyGenerator::class;
    }
);
```

The same goes for translator services. Implement `fork\altify\connectors\translation\TranslatorInterface` and register
your translator like this:

```
use fork\altify\events\RegisterTranslatorsEvent;
use fork\altify\services\Translator;

Event::on(
    Translator::class,
    Translator::EVENT_REGISTER_TRANSLATORS,
    function (RegisterTranslatorsEvent $event) {
        $event->translators['myTranslator'] = MyTranslator::class;
    }
);
```

---

## TODO

* Make translation services site based to respect languages
* Implement more alt text generation services
* Maybe implement a self-hosted alt text generation service
* Maybe implement an alt text generation service running in browser with TensorFlow JS or something similar
* Implement an alt text generator registering event
* Make public on GitHub, release on Packagist and the Craft Plugin Store

---

<table>
  <tr>
    <td><a href="https://www.fork.de"><img src="./assets/heart.png" width="38" height="41" alt="Fork Logo" /></a></td>
    <td>Brought to you by <a href="https://www.fork.de">Fork Unstable Media GmbH</a></td>
  </tr>
</table>

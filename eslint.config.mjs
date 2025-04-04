import globals from "globals";
import pluginJs from "@eslint/js";
import babelParser from "@babel/eslint-parser";

/** @type {import('eslint').Linter.Config[]} */

export default [
  pluginJs.configs.recommended,
  {
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node,
        ...globals.jquery,
        "wp": "readonly",
        "__dirname": "readonly",
        "orderData": "readonly",
        "WooPluginAjaxCheckout": "readonly",
        "WooPluginAjaxError": "readonly",
        "widgetSettings": "readonly",
        "pluginUrlPrefix": "readonly",
        "pluginPrefix": "readonly",
        "pluginTextDomain": "readonly",
        "pluginTextName": "readonly",
        "pluginName": "readonly",
        "pluginWidgetName": "readonly",
      },
      parser: babelParser,
      parserOptions: {
        requireConfigFile: false,
        babelOptions: {
          babelrc: false,
          configFile: false,
          presets: ["@babel/preset-react"],
        }
      }
    },
    plugins: {
      '@wordpress': {
        rules: {
          // You can use the 'rules' key to override specific settings in the WooCommerce plugin
          '@wordpress/i18n-translator-comments': 'warn',
          '@wordpress/valid-sprintf': 'warn',
        },
      },
      'jsdoc': {
        rules: {
          'jsdoc/check-tag-names': [
            'error',
            { definedTags: [ 'jest-environment' ] },
          ],
        },
      },
    },
    rules: {
      "no-unused-vars": "warn",
    }
  }
];

// eslint.config.js
import vuePlugin from 'eslint-plugin-vue'
import globals from 'globals'
import vueParser from 'vue-eslint-parser'

export default [
  {
    files: ['**/*.vue'],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node
      },
      parser: vueParser,
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module',
        parser: {
          js: '@babel/eslint-parser',
          requireConfigFile: false
        }
      }
    },
    plugins: {
      vue: vuePlugin
    },
    rules: {
      // Vue-specific rules
      'vue/html-self-closing': 'error',
      'vue/multi-word-component-names': 'off',
      // Правила для выравнивания атрибутов в Vue
      'vue/first-attribute-linebreak': ['error', {
        singleline: 'ignore',    // Для однострочных элементов не проверять
        multiline: 'below'       // Для многострочных - переносить атрибуты на новую строку
      }],
      'vue/max-attributes-per-line': ['error', {
        singleline: 1,           // Максимум 3 атрибута в одной строке
        multiline: 1             // Для многострочных - 1 атрибут на строку
      }],
      'vue/attributes-order': ['error', {
        order: [
          'DEFINITION',
          'LIST_RENDERING',
          'CONDITIONALS',
          'RENDER_MODIFIERS',
          'GLOBAL',
          'UNIQUE',
          'TWO_WAY_BINDING',
          'OTHER_DIRECTIVES',
          'OTHER_ATTR',
          'EVENTS',
          'CONTENT'
        ],
        alphabetical: false      // Не сортировать атрибуты по алфавиту
      }],
      'vue/html-closing-bracket-newline': ['error', {
        singleline: 'never',     // Для однострочных элементов - без переноса
        multiline: 'always'      // Для многострочных - всегда перенос
      }],
      'vue/html-indent': ['error', 2, {
        attribute: 1,            // Отступ для атрибутов
        baseIndent: 1,           // Базовый отступ
        closeBracket: 0,         // Отступ для закрывающего тега
        alignAttributesVertically: true  // Выравнивать атрибуты вертикально
      }],
      // JavaScript rules
      'semi': ['error', 'never'],
      'comma-dangle': ['error', 'never'],
      'quotes': ['error', 'single'],
      'indent': ['error', 2]
      // 'max-len': ['error', {
      //   code: 130,                   // Максимальная длина строки
      //   tabWidth: 2,                 // Ширина табуляции (совпадает с indent)
      //   ignoreUrls: true,           // Игнорировать URL-адреса
      //   ignoreStrings: true,         // Игнорировать строковые литералы
      //   ignoreTemplateLiterals: true, // Игнорировать шаблонные строки
      //   ignoreRegExpLiterals: true,  // Игнорировать регулярные выражения
      //   ignoreComments: true         // Игнорировать комментарии
      // }]
    }
  },
  {
    files: ['**/*.js'],
    languageOptions: {
      globals: {
        ...globals.browser,
        ...globals.node
      },
      parserOptions: {
        ecmaVersion: 'latest',
        sourceType: 'module'
      }
    },
    rules: {
      'semi': ['error', 'never'],
      'comma-dangle': ['error', 'never'],
      'quotes': ['error', 'single'],
      'indent': ['error', 2],
      'max-len': ['error', {
        code: 130,
        tabWidth: 4,
        ignoreUrls: true,
        ignoreStrings: true,
        ignoreTemplateLiterals: true,
        ignoreRegExpLiterals: true,
        ignoreComments: true
      }]
    }
  }
]

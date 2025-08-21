/**
 * Plugin для TinyMCE, который добавляет кнопку генерации контента ReadyScript
 */
window.tinymce.PluginManager.add('rsaigenerate', function(editor, url) {
  // Регистрируем иконку
  editor.ui.registry.addIcon('icon-ai', '<svg width="21" height="21" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"><path d="M62.919,15.394L50.378,4.465c-0.624-0.544-1.571-0.479-2.116,0.146L25.376,30.872c-0.263,0.302-0.395,0.697-0.365,1.097  l0.87,11.661c0.059,0.788,0.715,1.389,1.494,1.389c0.032,0,0.063-0.001,0.095-0.003l11.67-0.732c0.4-0.025,0.773-0.209,1.037-0.512  L63.064,17.51c0.262-0.3,0.393-0.691,0.365-1.088C63.402,16.025,63.219,15.655,62.919,15.394z M38.328,41.328l-9.565,0.601  l-0.713-9.558l14.675-16.839l10.278,8.958L38.328,41.328z M54.974,22.228L44.695,13.27l4.843-5.558l10.279,8.958L54.974,22.228z"/><path d="M37.892,54.521c-10.753,1.38-24.135,3.096-31.017-0.391c-2.748-1.393-3.363-2.392-3.304-2.726  c0.318-1.804,8.396-4.33,17.287-5.405c0.822-0.1,1.408-0.847,1.309-1.669c-0.1-0.822-0.847-1.402-1.669-1.31  c-4.434,0.535-18.969,2.685-19.881,7.863c-0.556,3.156,3.552,5.238,4.902,5.922c7.706,3.905,21.013,2.198,32.755,0.69  c7.094-0.909,15.923-2.043,17.231-0.452c0.154,0.188,0.037,0.635-0.046,0.885c-0.262,0.786,0.162,1.636,0.948,1.897  c0.157,0.053,0.317,0.077,0.475,0.077c0.628,0,1.214-0.397,1.423-1.025c0.618-1.854,0.078-3.057-0.483-3.739  C55.57,52.402,49.113,53.084,37.892,54.521z"/></svg>');
  editor.ui.registry.addIcon('icon-ai-loading', '<svg width="24" height="19" viewBox="0 0 24 19" xmlns="http://www.w3.org/2000/svg">\n' +
      '  <style>\n' +
      '    circle {\n' +
      '      fill: #888;\n' +
      '      opacity: 0.9;\n' +
      '    }\n' +
      '    .dot1 { animation: move-dots 1s infinite linear; }\n' +
      '    .dot2 { animation: move-dots 1s infinite linear 0.33s; }\n' +
      '    .dot3 { animation: move-dots 1s infinite linear 0.66s; }\n' +
      '    @keyframes move-dots {\n' +
      '      0%, 100% { transform: translateY(0); }\n' +
      '      20% { transform: translateY(-5px); }\n' +
      '      40% { transform: translateY(5px); }\n' +
      '      60% { transform: translateY(0); }\n' +
      '      80% { transform: translateY(-5px); }\n' +
      '    }\n' +
      '  </style>\n' +
      '  <circle class="dot1" cx="4" cy="9.5" r="3.5"/>\n' +
      '  <circle class="dot2" cx="12" cy="9.5" r="3.5"/>\n' +
      '  <circle class="dot3" cx="20" cy="9.5" r="3.5"/>\n' +
      '</svg>');

  // Добавляем кнопку в панель инструментов
  let $element = $(editor.getElement());
  let aiSettings = $element.data('aiRichtext');

  if (aiSettings) {
    editor.rs = {
      stopGeneration: function () {
        return $element.aiRichText('stopGeneration');
      },

      startGeneration: function(api, promptId, force) {
        if (api.isDisabled()) {
          return Promise.resolve();
        }

        editor.rs.previousValue = editor.getContent();
        return $element.aiRichText('startGeneration', promptId).get(0).catch(() => {});
      }
    }

    editor.ui.registry.addSplitButton('rsaigenerate', {
      icon: 'icon-ai',
      tooltip: lang.t('Заполнить через ИИ'),
      fetch: function (callback) {
        let items = aiSettings.prompts.map(function (action) {
          return {
            type: 'choiceitem',
            text: action.note,
            value: action.id
          };
        });
        callback(items);
      },
      onSetup: function (api) {
        $element
            .closest('.ui-dialog')
            .on('dialogclose', editor.rs.stopGeneration);

        $element
            .on('aiStartLoading', () => {
              api.setActive(true);
              if (api.setIcon) {
                api.setIcon('icon-ai-loading');
              }
            })
            .on('aiEndLoading', () => {
              api.setActive(false);
              if (api.setIcon) {
                api.setIcon('icon-ai');
              }
            })
            .on('aiBeforeFirstSetValue', (event, fullText) => {
              editor.undoManager.add();
            })
            .on('aiSetValue', (event, fullText) => {
              editor.undoManager.transact(() => {
                editor.setContent(fullText);
              });
            });

        if (!aiSettings.prompts.length
            || $(editor.getElement()).closest('.multi_edit_rightcol').length) {
          api.setDisabled(true);
        }

        editor.rs.aiGenerateApi = api;
      },
      onAction: function (api, force) {
        let defaultPromptId = aiSettings.prompts[0].id;
        this.onItemAction(api, defaultPromptId, force);
      },
      onItemAction: function (api, promptId, force) {
        editor.rs.startGeneration(api, promptId, force);
      }
    });
  }

  return {
    getMetadata: function() {
      return {
        name: 'ReadyScript TinyMCE AI Plugin',
        url: 'https://readyscript.ru'
      };
    }
  };
});
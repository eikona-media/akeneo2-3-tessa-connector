/* eslint-disable indent */
/*
 * field.js
 *
 * @author    Matthias Mahler <m.mahler@eikona.de>
 * @copyright 2017 Eikona AG (http://www.eikona.de)
 */

'use strict';

define([
    'pim/form/common/fields/field',
    'underscore',
    'eikona/tessa/connector/templates/product/field/mam',
    'pim/form-builder',
    'routing',
    'oro/loading-mask'
  ], function (BaseField,
  _,
  fieldTemplate,
  FormBuilder,
  Routing,
  LoadingMask) {
    let modalBox;
    let $self;

    return BaseField.extend({
      fieldTemplate: _.template(fieldTemplate),
      events: {
        'click .add-asset': 'openModal'
      },
      initialize (...args) {
        $self = this;
        this.setOptions([]);
        BaseField.prototype.initialize.apply(this, args);

        return this;
      },
      renderInput () {
        return this.renderField(this.getAssetIdsArray());
      },
      renderField (values) {
        const assets = [];
        _.each(values, function (item) {
          if (!item) {
            return;
          }
          assets.push({
            id: item,
            url: Routing.generate(
              'eikona_tessa_media_preview',
              {assetId: item}
            ),
            linkUrl: Routing.generate(
              'eikona_tessa_media_detail',
              {assetId: item}
            )
          });
        });

        return this.fieldTemplate({
          value: values.join(','),
          assets
        });
      },
      openModal () {
        const deferred = $.Deferred();

        $(window)
          .on('message', this.receiveMessage.bind(this));

        FormBuilder.build('eikon-tessa-asset-selection-iframe')
          .then(function (form) {
            modalBox = new Backbone.BootstrapModal({
              modalOptions: {
                backdrop: 'static',
                keyboard: false
              },
              allowCancel: true,
              okCloses: false,
              title: _.__('tessa.asset management.title'),
              content: '',
              cancelText: _.__('tessa.asset management.cancel'),
              okText: _.__('tessa.asset management.confirm')
            });
            modalBox.open();
            modalBox.$el.addClass('EikonModalAssetsSelection');
            form.setElement(modalBox.$('.modal-body'))
              .render();

            modalBox.$el.find('iframe')
              .on('load', this.onIframeReady)
              .prop('src', this.getUrl());

            modalBox.on('cancel', deferred.reject);
            modalBox.on('ok', function () {
              const assets = _.sortBy(form.getAssets(), 'code');
              modalBox.close();
              $(window)
                .off('message', this.receiveMessage);
              deferred.resolve(assets);
            }.bind(this));

            const loadingMask = new LoadingMask();
            loadingMask.render()
              .$el
              .appendTo(modalBox.$el.find('.modal-body'))
              .css({
                'position': 'absolute',
                'width': '100%',
                'height': '100%',
                'top': '0',
                'left': '0'
              });
            loadingMask.show();

            setTimeout(function () {
              loadingMask.hide()
                .$el
                .remove();
            }, 5000);
          }.bind(this));

        return deferred.promise();
      },
      getUrl () {
        const data = {
          'ProductId': this.getUniqueId(),
          'attribute': JSON.stringify({
            // eslint-disable-next-line camelcase
            allowed_extensions: this.getOption('allowedExtensions'),
            properties: {maximumCount: this.getOption('maximumCount')}
          }),
          'context': 'reference-data'
        };

        return Routing.generate('eikona_tessa_media_select', {
          data: jQuery.param(data),
          t: Math.floor(Date.now() / 1000)
        });
      },
      onIframeReady (e) {
        console.log('iframe ready');
        const iframe = e.target;
        const iframeContent = iframe.contentWindow;

        if (!iframe.src) {
          return;
        }

        iframeContent.postMessage(JSON.stringify({'selected': $self.getAssetIdsArray()}), '*');
      },
      receiveMessage (event) {
        console.log(`Hi! Yes, the App has got a message ;) Senders origin: ${event.originalEvent.origin}`);

        const sids = [];
        $.each(JSON.parse(event.originalEvent.data), function (i, v) {
          sids.push(v.position_asset_system_id);
        });

        this.updateModel(this.getRecomposedValueForModel(sids.join(',')));

        this.render();

        if (modalBox) {
          modalBox.close();
        }

        $(window)
          .off('message');
      },
      isNewEntity () {
        const form = this.$el.closest('form');

        return form.find('input[type=hidden][name*=identifier]')
          .val() === '[null]';
      },
      getUniqueId () {


        if (!this.identifier) {
          this.identifier = this.extractUniqueIdFromValue(this.getModelValue());
        }

        if (!this.identifier) {
          this.identifier = this.getNewUniqueId();
        }

        return this.identifier;
      },
      setUniqueId (value) {
        this.identifier = value;
      },
      getNewUniqueId () {
        const prefix = this.config.customEntityName ? this.config.customEntityName.toUpperCase() : '';

        return `RD.${prefix}.${Date.now()}`;
      },
      extractUniqueIdFromValue (value) {
        if (value === undefined || value.indexOf(':') < 0) {
          return null;
        }

        return value.substr(0, value.indexOf(':'));
      },
      extractAssetIdsFromValue (value) {
        if (value === undefined || value.indexOf(':') < 0) {
          return null;
        }

        return value.substr(value.indexOf(':') + 1);
      },
      getAssetIdsArray () {
        const assetIdString = this.extractAssetIdsFromValue(this.getModelValue());
        if (typeof assetIdString === 'string') {
          return assetIdString.split(',');
        }

        return [];
      },
      getRecomposedValueForModel (sids) {
        return [this.getUniqueId(), sids].join(':');
      },
      setOptions (options) {
        this.options = options;
      },
      getOption (name) {
        if (this.options[name]) {
          return this.options[name];
        }

        return null;
      }
    });
  }
);

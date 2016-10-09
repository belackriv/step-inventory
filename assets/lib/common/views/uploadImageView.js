"use strict";

import jquery from 'jquery';
import _ from 'underscore';
import Backbone from 'backbone';
import Radio from 'backbone.radio';
import Marionette from 'marionette';

import ImageModel from 'lib/common/models/uploadedImageModel.js';
import viewTpl from  "./uploadImageView.hbs!";

export default Marionette.View.extend({
  template: viewTpl,
  ui: {
    'imageDisplay': '[data-ui-name="imageDisplay"]',
    'dropTarget': '[data-ui-name="dropTarget"]',
    'openAddFileButton': 'button[name="openAddFile"]',
    'imageFileInput': 'input[name="image"]',
    'uploadButton': 'button[data-ui-name="upload"]',
    'cancelButton': 'button[data-ui-name="cancel"]',
    'form': 'form',
  },
  events: {
    'click @ui.openAddFileButton': 'onOpenAddFileButtonClick',
    'change @ui.imageFileInput': 'addSelectedFile',
    'click @ui.uploadButton': 'uploadImage',
    'submit @ui.form': 'uploadImage',
    'click @ui.cancelButton': 'cancel',
  },
  onRender(){
    let dropTarget = this.ui.dropTarget.get(0);
    ['dragenter','dragover','dragleave','dragstop','drop'].forEach(
      event => dropTarget.addEventListener(event, this.stopNormalBehavior.bind(this), false));
    ['dragenter','dragover'].forEach(
      event => dropTarget.addEventListener(event, this.showFilesHover.bind(this), false));
    ['drop','dragleave','dragstop'].forEach(
      event => dropTarget.addEventListener(event, this.hideFilesHover.bind(this), false));
    window.addEventListener('dragover', this.stopNormalBehavior.bind(this), false);
    window.addEventListener('drop', this.stopNormalBehavior.bind(this), false);
    dropTarget.addEventListener('drop', this.addDroppedFiles.bind(this), false);
  },
  stopNormalBehavior(event){
    event.preventDefault();
    event.stopPropagation();
  },
  showFilesHover(event){
    if(event.dataTransfer.files.length > 1){
      this.ui.dropTarget.addClass('is-danger');
    }else{
      this.ui.dropTarget.addClass('is-success');
    }
  },
  hideFilesHover(event){
    this.ui.dropTarget.removeClass('is-success is-danger');
  },
  onOpenAddFileButtonClick(event){
    this.ui.imageFileInput.click();
  },
  addDroppedFiles(event){
    if(event.dataTransfer.files.length > 0){
      this.addFile(event.dataTransfer.files[0]);
    }
  },
  addSelectedFile(event){
    if(event.target.files.length > 0){
      this.addFile(event.target.files[0]);
    }
  },
  addFile(file){
    this.imageFile = file;
    var fileReader = new FileReader();
    fileReader.onload = ()=>{
        this.ui.imageDisplay.attr('src', fileReader.result);
    };
    fileReader.readAsDataURL(file);
  },
  imageFile: null,
  uploadImage(event){
    event.preventDefault();
    this.disableButtons();
    if(this.imageFile){
      let view = this;
      let image = new ImageModel({
        organization: this.options.organization
      });
      let formData = new FormData();
      formData.append('image', this.imageFile);
      jquery.ajax({
        url: '/organization/'+this.options.organization.id+'/upload_image',
        data: formData,
        processData: false,
        contentType: false,
        type: 'POST',
        success(data){
          image.set(data);
          view.assignImageToModelProperty(image);
        },
      });
    }
  },
  assignImageToModelProperty(image){
    this.model.set(this.options.imageAttributeName, image);
    this.model.save().then(()=>{
      Radio.channel('dialog').trigger('close');
    });
  },
  cancel(){
    Radio.channel('dialog').trigger('close');
  },
  disableButtons(){
    this.ui.uploadButton.prop('disabled', true).addClass('is-loading');
    this.ui.cancelButton.prop('disabled', true);
  },
  enableButtons(){
    this.ui.uploadButton.prop('disabled', false).removeClass('is-loading');
    this.ui.cancelButton.prop('disabled', false);
  },
});

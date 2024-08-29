<template>
    <v-app>
      <v-container>
        <v-file-input
          v-model="file"
          label="Upload Image"
          @change="onFileChange"
          accept="image/*"
        ></v-file-input>
  
        <div v-if="imageSrc" class="crop-container">
          <img
            ref="cropperImage"
            :src="imageSrc"
            class="crop-image"
          />
        </div>
  
        <v-btn @click="cropImage" color="primary">Crop Image</v-btn>
        <v-btn @click="uploadImage" color="success">Upload Cropped Image</v-btn>
        <v-btn @click="resetImage" color="error">Reset Image</v-btn>
      </v-container>
    </v-app>
  </template>
  
  <script>
  import Cropper from 'cropperjs'
  import 'cropperjs/dist/cropper.css'
  
  export default {
    data() {
      return {
        file: null,
        imageSrc: null,
        originalImageSrc: null,
        croppedImage: null,
        cropper: null
      }
    },
    watch: {
      imageSrc(newSrc) {
        if (this.cropper) {
          this.cropper.destroy()
        }
        this.$nextTick(() => {
          this.cropper = new Cropper(this.$refs.cropperImage, {
            aspectRatio: 1,
            responsive: true
          })
        })
      }
    },
    methods: {
      onFileChange(event) {
        const file = event.target.files[0]
        if (file && file.type.startsWith('image/')) {
          const reader = new FileReader()
          reader.onload = (e) => {
            this.imageSrc = e.target.result
            this.originalImageSrc = e.target.result 
          }
          reader.readAsDataURL(file)
        }
      },
      cropImage() {
        if (this.cropper) {
          const croppedCanvas = this.cropper.getCroppedCanvas()
          this.croppedImage = croppedCanvas.toDataURL()
          this.imageSrc = this.croppedImage  
        }
      },
      resetImage() {
        // Reset image to the original state
        if (this.originalImageSrc) {
          this.imageSrc = this.originalImageSrc
        }
      },
      uploadImage() {
        if (this.croppedImage) {
       
          console.log('Cropped Image Data URL:', this.croppedImage)
  
        }
      },
    }
  }
  </script>
  
  <style scoped>
  .crop-container {
    position: relative;
    width: 100; /* Adjust width as needed */
    height: 300px; /* Adjust height as needed */
    overflow: hidden;
    margin-bottom: 16px;
  }
  
  .crop-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }
  </style>
  
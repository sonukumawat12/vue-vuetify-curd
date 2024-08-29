<template>
  <v-app>
    <v-btn color="primary" @click="addUserFrom" class="ml-4">
      <v-icon class="bdsd">mdi-plus</v-icon>Add</v-btn>

    <v-card title="Users">
      <template v-slot:text>
        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify" variant="outlined" hide-details
          single-line></v-text-field>
      </template>

      <v-data-table :headers="headers" :items="desserts" :search="search" @click:row="handleRowClick">
        <template v-slot:item.profile="{ item }">
          <v-img :src="`${item.profile}`" max-height="60" max-width="80" contain class="rounded-xl"></v-img>
        </template>
        <template v-slot:item.action="{ item }">
          <v-btn icon @click.stop="editItem(item)">
            <v-icon>mdi-pencil</v-icon>
          </v-btn>
          <v-btn icon @click.stop="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>


    </v-card>

    <div class="text-center pa-4">
      <v-dialog v-model="dialog" max-width="600">
        <v-card>
          <v-card-title>
            <span class="headline">{{ formTitle }}</span>
          </v-card-title>

          <v-card-text>
            <!-- <v-img :src="showData.profile" max-height="60" max-width="80" contain class="rounded-xl"></v-img> -->
            <v-sheet class="mx-auto" max-width="500">
              <v-form @submit.prevent="btnValue === 'Save' ? saveForm() : updateForm()">
                <div>
                  <v-img v-if="!isHiddenImg" :width="150" v-on:click="isHiddenImg = true" aspect-ratio="16/9" cover
                    :src="`${showData.profile}`" class="showProfileImg"></v-img>
                </div>
                <div v-if="imageSrc" class="crop-container">
                  <img ref="cropperImage" :src="imageSrc" class="crop-image" />
                </div>
                <!-- <v-img :src="`https://cdn-icons-png.flaticon.com/512/9385/9385270.png`" width="100"
                  v-model="addProfileIcon" class="addProfileI"></v-img> -->
                <v-file-input v-model="file" label="Upload Image" @change="onFileChange" accept="image/*"
                  v-if="showFileInput === 'show'"></v-file-input>



                <!-- <v-btn @click="cropImage" color="primary">Crop Image</v-btn>
                <v-btn @click="resetImage" color="error">Reset Image</v-btn> -->

                <v-text-field v-model="showData.name" :rules="firstNameRules" label="Full Name"
                  :disabled="isDisabled"></v-text-field>
                <v-text-field v-model="showData.email" :rules="emailAddressRules" label="Email Address"
                  :disabled="isDisabled"></v-text-field>
                <v-text-field v-model="showData.phone" :rules="phoneNumberRules" label="Phone Number"
                  :disabled="isDisabled"></v-text-field>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn @click="closeDialog">Close</v-btn>
                  <v-btn type="submit" color="primary" v-if="btnValue !== ''">{{ btnValue }}</v-btn>
                </v-card-actions>
              </v-form>
            </v-sheet>
          </v-card-text>
        </v-card>
      </v-dialog>
    </div>

  </v-app>
</template>

<script>
import axios from 'axios'
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Cropper from 'cropperjs'
import 'cropperjs/dist/cropper.css'

export default {
  data() {
    return {
      search: '',
      dialog: false,
      file: null,
      imageSrc: null,
      isHiddenImg: false,
      originalImageSrc: null,
      originalImagepath: null,
      addProfileIcon: null,
      isDisabled: false,
      croppedImage: null,
      cropper: null,
      headers: [
        { align: 'start', key: 'profile', sortable: false, title: 'Profile' },
        { key: 'name', title: 'Name' },
        { key: 'email', title: 'Email' },
        { key: 'phone', title: 'Phone No.' },
        { key: 'action', title: 'Action' },
      ],
      desserts: [],
      userId: null,
      btnValue: 'Save',
      formTitle: 'Add User',
      showData: {
        name: null,
        email: null,
        phone: null,
        profile: null
      },
      firstNameRules: [
        value => (value?.length >= 3) || 'Full name must be at least 3 characters.',
      ],
      emailAddressRules: [
        value => /^[a-z.-]+@[a-z.-]+\.[a-z]+$/i.test(value) || 'Must be a valid e-mail.',
      ],
      phoneNumberRules: [
        value => /^[0-9-]{10,}$/.test(value) || 'Phone number needs to be at least 10 digits.',
      ],
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
    handleRowClick(e, { item }) {
      console.log('row', item);
      this.addUserFrom();
      this.btnValue = '';
      this.showData = { ...item };
      this.formTitle = '';
      this.isDisabled = true;
      this.showFileInput = 'hide'
    },
    // get data for dataTable
    async getData() {
      try {
        const response = await axios.get('http://127.0.0.1:8000/api/get/users');
        this.desserts = response.data;
      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },


    validateForm() {
      return this.firstNameRules.every(rule => rule(this.showData.name) === true) &&
        this.emailAddressRules.every(rule => rule(this.showData.email) === true) &&
        this.phoneNumberRules.every(rule => rule(this.showData.phone) === true);
    },

    // from post method for Data save
    async saveForm() {
      if (!this.validateForm()) {
        return;
      }

      const formData = new FormData();
      formData.append('name', this.showData.name);
      formData.append('email', this.showData.email);
      formData.append('phone', this.showData.phone);

      if (this.file) {
        formData.append('profile', this.file);
      }

      try {
        const response = await axios.post('http://127.0.0.1:8000/api/store/user', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        this.closeDialog();
        this.getData();
        toast.success('Data saved successfully!', {
          autoClose: 2000,
          theme: 'dark'
        });
      } catch (error) {
        console.error('Error:', error);
      }
    },


    // from get method
    editItem(item) {
      this.isDisabled = false;
      this.dialog = true;
      this.formTitle = "Edit user";
      this.showFileInput = 'show';
      this.btnValue = "Update";
      this.showData = { ...item };
    },


    // from post method for update
    updateForm() {
      if (!this.validateForm()) return;

      const updatedData = new FormData();
      updatedData.append('name', this.showData.name);
      updatedData.append('email', this.showData.email);
      updatedData.append('phone', this.showData.phone);
      if (this.file) {
        updatedData.append('profile', this.file);
      }

      axios.post(`http://127.0.0.1:8000/api/update/user/${this.showData.id}`, updatedData)
        .then(response => {
          this.closeDialog();
          this.getData();
          toast.success('Record updated successfully!', {
            autoClose: 2000,
            theme: 'dark'
          });
        })
        .catch(error => {
          console.error('Error:', error);
        });
    },

    // add form
    addUserFrom() {
      this.isDisabled = false;
      this.showFileInput = 'show';
      this.dialog = true;
      this.btnValue = "Save";
      this.formTitle = "Add user";
      this.showData = {
        name: null,
        email: null,
        phone: null,
        profile: null
      };
    },

    // delete records
    deleteItem(item) {
      if (confirm('Are you sure you want to delete this item?')) {
        axios.delete(`http://127.0.0.1:8000/api/delete/user/${item.id}`)
          .then(response => {
            this.getData();
            toast.success('Record deleted successfully!', {
              autoClose: 2000,
              theme: 'dark'
            });
          })
          .catch(error => {
            console.error('Error:', error);
            alert(error);
          });
      }
    },

    closeDialog() {
      this.dialog = false;
    },

    onFileChange(event) {
      this.isHiddenImg = true;
      console.log(this.isHiddenImg)
      const file = event.target.files[0];
      this.originalImagepath = file;
      if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
          this.imageSrc = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    },

    async cropImage() {
      if (this.cropper) {
        const croppedCanvas = this.cropper.getCroppedCanvas();
        this.croppedImage = croppedCanvas.toDataURL();  // This is a base64 image data

        // Convert base64 to File object
        const response = await fetch(this.croppedImage);
        const blob = await response.blob();
        this.file = new File([blob], "cropped_image.jpg", { type: blob.type });
      }
    },

    resetImage() {
      this.imageSrc = this.originalImageSrc;
      if (this.cropper) {
        this.cropper.destroy();
        this.cropper = new Cropper(this.$refs.cropperImage, {
          aspectRatio: 1,
          responsive: true
        });
      }
    },


  },
  created() {
    this.getData();
  }
}
</script>

<style scoped>
.crop-container {
  position: relative;
  left: 152px;
  top: -61px;
  width: 200px;
  height: 200px;
  overflow: hidden;
  border-radius: 200px;
}

.v-responsive.v-img.showProfileImg[data-v-0c791fc2] {
  width: 74px !important;
  height: 70px;
  border-radius: 100%;
  position: relative;
  border: 2px solid grey;
  top: -30px;
  left: 197px;
}

.crop-image {
  width: 100%;
  height: auto;
}
</style>
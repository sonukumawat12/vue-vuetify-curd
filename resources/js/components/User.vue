<template>
  <v-app>
    <v-btn color="primary" @click="addUserFrom" class="ml-4">
      <v-icon class="bdsd">mdi-plus</v-icon>Add</v-btn>

    <v-card title="Users">
      <template v-slot:text>
        <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify" variant="outlined" hide-details
          single-line></v-text-field>
      </template>

      <v-row no-gutters>

        <v-col>
          <v-sheet class="pa-2 ma-2">
            <v-select label="Select" v-model="Record_by_status" dense :items="['Active', 'In-active']"></v-select>
          </v-sheet>
        </v-col>
        <v-icon @click="resetFilters" indeterminate>mdi-restart</v-icon>
        <v-col>
          <v-sheet class="pa-2 ma-2">
            <v-btn @click="dialogDate = true">
              Filter records by data
            </v-btn>

            <v-dialog v-model="dialogDate" width="auto">
              <v-card max-width="400">

                <v-card-text>
                  <v-date-picker show-adjacent-months v-model="Record_by_date"></v-date-picker>
                </v-card-text>

                <template v-slot:actions>
                  <v-btn class="ms-auto" text="Close" @click="dialogDate = false"></v-btn>
                  <v-btn class="ms-auto" text="Filter" @click="Record_by_DateFormate"></v-btn>
                </template>

              </v-card>
            </v-dialog>

          </v-sheet>
        </v-col>

      </v-row>

      <v-data-table :headers="headers" :items="desserts" :search="search" @click:row="handleRowClick">
        <template v-slot:item.profile="{ item }">
          <v-img :src="`${item.profile}`" max-height="60" max-width="80" contain class="rounded-xl"></v-img>
        </template>

        <template v-slot:item.email="{ item }">

          <v-tooltip :text=item.email>
            <template v-slot:activator="{ props }">
              <v-btn v-bind="props" @click.stop="copyEmailAdd(item.email)" class="cssResetBtn">
                <span v-if="item.email.length > 15">{{ item.email.slice(0, 15) }}...</span>
                <span v-else>{{ item.email }}</span></v-btn>
            </template>
          </v-tooltip>

        </template>

        <template v-slot:item.created_at="{ item }">
          <span>{{ getDate(item.created_at) }}</span>
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

                <div class="Main-img-section">
                  <v-img v-if="!isHiddenImg" :width="150" aspect-ratio="16/9" cover :src="`${showData.profile}`"
                    class="showProfileImg"></v-img>

                  <v-img :src="`https://cdn-icons-png.flaticon.com/512/9385/9385270.png`" width="100"
                    @click="toggleFileInput" v-if="iconFileselect" class="addProfileI">

                  </v-img>
                </div>
                <div v-if="imageSrc" class="crop-container">
                  <img ref="cropperImage" :src="imageSrc" class="crop-image" />
                </div>

                <v-file-input ref="fileInput" v-model="file" label="Upload Image" @change="onFileChange"
                  accept="image/*" style="display: none;">
                </v-file-input>

                <v-btn @click="cropImage" color="primary" v-if="cropButton">Crop Image</v-btn>
                <!--    <v-btn @click="resetImage" color="error">Reset Image</v-btn> -->

                <v-text-field v-model="showData.name" :rules="firstNameRules" label="Full Name" :disabled="isDisabled">
                </v-text-field>

                <v-text-field v-model="showData.email" :rules="emailAddressRules" label="Email Address"
                  :disabled="isDisabled">
                </v-text-field>

                <v-text-field v-model="showData.phone" :rules="phoneNumberRules" label="Phone Number"
                  :disabled="isDisabled"></v-text-field>

                <v-select label="Select" v-model="showData.status" :items="['Active', 'In-active']"></v-select>

                <v-card-actions>
                  <v-spacer></v-spacer>
                  <v-btn @click="closeDialog">Close</v-btn>
                  <v-btn type="submit" color="primary" v-if="btnValue !== ''">{{ btnValue }}
                    <v-progress-circular style="width: 15px;" color="primary" v-if="saveSpinner"
                      indeterminate></v-progress-circular>
                  </v-btn>
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
import moment from 'moment';
export default {
  data() {
    return {

      search: '',
      Record_by_date: null,
      dialog: false,
      dialogDate: false,
      file: null,
      Record_by_status: null,
      cropButton: false,
      iconFileselect: true,
      imageSrc: null,
      isHiddenImg: false,
      originalImageSrc: null,
      originalImagepath: null,
      addProfileIcon: null,
      isDisabled: false,
      croppedImage: null,
      cropper: null,
      saveSpinner: false,

      headers: [
        { align: 'start', key: 'profile', sortable: false, title: 'Profile' },
        { key: 'name', title: 'Name' },
        { key: 'email', title: 'Email' },
        { key: 'phone', title: 'Phone No.' },
        { key: 'status', title: 'Status', sortable: false, },
        { key: 'created_at', title: 'Created Date' },
        { key: 'action', title: 'Action', sortable: false, },
      ],

      desserts: [],
      userId: null,
      btnValue: 'Save',
      formTitle: 'Add User',
      showData: {
        name: null,
        email: null,
        phone: null,
        profile: null,
        status: ''
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
        console.log('new img selected');
        this.cropper.destroy();
      }
      this.$nextTick(() => {
        this.cropper = new Cropper(this.$refs.cropperImage, {
          aspectRatio: 1,
          responsive: true,
        });
      });
    },
    Record_by_status(newStatus) {
      console.log(newStatus)
      this.getData_by_Status(newStatus);
    },

  }
  ,

  methods: {
    Record_by_DateFormate() {
      this.dialogDate = false;
      const formattedDate = moment(String(this.Record_by_date)).format('YYYY-MM-DD');
      this.getData_by_Date(formattedDate);
    },
    resetFilters() {
      // console.log('first')
      this.getData();
    },
    getDate(datetime) {
      let date = new Date(datetime).toJSON().slice(0, 10).replace(/-/g, '/')
      return date
    },
    handleRowClick(e, { item }) {
      console.log('row', item);
      this.addUserFrom();
      this.btnValue = '';
      this.showData = { ...item };
      this.formTitle = '';
      this.cropButton = false;
      this.imageSrc = false;
      this.iconFileselect = false;
      this.croppedImage = '';
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
    async getData_by_Status(status) {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/get/users/${status}`);
        this.desserts = response.data;

      } catch (error) {
        console.error('Error fetching data:', error);
      }
    },
    async getData_by_Date(date) {
      try {
        const response = await axios.get(`http://127.0.0.1:8000/api/get/users-by-date/${date}`);
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
    copyEmailAdd(email) {
      console.log('copy', email)
      navigator.clipboard.writeText(email);
      alert('Email address copied successfully.')
    },
    // from post method for Data save
    async saveForm() {
      this.saveSpinner = true;
      if (!this.validateForm()) {
        return;
      }

      const formData = new FormData();
      formData.append('name', this.showData.name);
      formData.append('email', this.showData.email);
      formData.append('phone', this.showData.phone);
      formData.append('status', this.showData.status);

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
        this.saveSpinner = false;
        toast.success('User created successfully!', {
          autoClose: 2000,
          theme: 'dark'
        });
      } catch (error) {
        console.error('Error:', error); this.saveSpinner = false;
      }

    },


    // from get method
    editItem(item) {

      this.cropButton = true;
      this.imageSrc = true;
      this.iconFileselect = true;
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
      this.isHiddenImg = false;
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
      this.cropButton = true;
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
      console.log('fs')
      if (this.cropper) {
        const croppedCanvas = this.cropper.getCroppedCanvas();
        this.croppedImage = croppedCanvas.toDataURL();  // This is a base64 image data

        // Convert base64 to File object
        const response = await fetch(this.croppedImage);
        const blob = await response.blob();
        this.file = new File([blob], "cropped_image.jpg", { type: blob.type });
      }
    },

    toggleFileInput() {
      this.$refs.fileInput.$el.querySelector('input[type="file"]').click();
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

/* .showProfileImg {
  width: 74px !important;
  height: 70px;
  border-radius: 100%;
  position: relative;
  border: 2px solid grey;
  top: -30px;
  left: 197px;
} */
.custom-email {
  max-width: 100px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis
}

.crop-image {
  width: 100%;
  height: auto;
}

.cssResetBtn {
  box-shadow: none;
  text-transform: capitalize;
}
</style>
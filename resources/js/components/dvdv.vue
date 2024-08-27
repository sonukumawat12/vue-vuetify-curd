<template>
    <v-container>
      <v-btn @click="addUserFrom">Add</v-btn>
  
      <v-card title="Users">
        <template v-slot:text>
          <v-text-field v-model="search" label="Search" prepend-inner-icon="mdi-magnify" variant="outlined" hide-details
            single-line></v-text-field>
        </template>
  
        <v-data-table :headers="headers" :items="desserts" :search="search">
          <template v-slot:item.action="{ item }">
            <v-btn icon @click="editItem(item)">
              <v-icon>mdi-pencil</v-icon>
            </v-btn>
          </template>
        </v-data-table>
      </v-card>
    </v-container>
  
    <div class="text-center pa-4">
      <v-dialog v-model="dialog" max-width="600" persistent>
        <v-card>
          <v-card-title>
            <span class="headline">{{formTitle}}</span>
          </v-card-title>
  
          <v-card-text>
            <v-sheet class="mx-auto" max-width="500">
              <v-form @submit.prevent="updateForm">
                <v-text-field v-model="showData.name" :rules="firstNameRules" label="Full Name" outlined
                  dense></v-text-field>
  
                <v-text-field v-model="showData.email" :rules="emailAddressRules" label="Email Address" outlined
                  dense></v-text-field>
  
                <v-text-field v-model="showData.phone" :rules="phoneNumberRules" label="Phone Number" outlined
                  dense></v-text-field>
              </v-form>
            </v-sheet>
          </v-card-text>
  
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn @click="closeDialog" text>Close</v-btn>
            <v-btn @click="updateForm" color="primary">{{btnValue}}</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    data() {
      return {
        search: '',
        dialog: false,
  
        headers: [
          { align: 'start', key: 'name', sortable: false, title: 'Name' },
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
          phone: null
        },
  
        firstNameRules: [
          value => {
            if (value?.length >= 3) return true
            return 'Full name must be at least 3 characters.'
          },
        ],
  
        emailAddressRules: [
          value => {
            if (/^[a-z.-]+@[a-z.-]+\.[a-z]+$/i.test(value)) return true
            return 'Must be a valid e-mail.'
          },
        ],
        
        phoneNumberRules: [
          value => {
            if (/^[0-9-]{10,}$/.test(value)) return true
            return 'Phone number needs to be at least 10 digits.'
          }
        ],
  
      }
    },
  
    methods: {
      async getData() {
        try {
          const response = await axios.get('http://127.0.0.1:8000/api/get/users');
          this.desserts = response.data;
        } catch (error) {
          console.error('Error fetching data:', error);
        }
      },
  
      editItem(item) {
        this.dialog = true;
        this.btnValue = "Update";
        this.formTitle = "Edit user";
        this.showData.name = item.name;
        this.showData.email = item.email;
        this.showData.phone = item.phone;
      },
  
      addUserFrom() {
        this.dialog = true;
        this.btnValue = "Save";
        this.formTitle = "Add user";
        this.showData = {
          name: null,
          email: null,
          phone: null
        };
      },
  
      updateForm() {
        const updatedData = {
          name: this.showData.name,
          email: this.showData.email,
          phone: this.showData.phone
        }
        axios.post(`http://127.0.0.1:8000/api/update/user/${this.userId}`, updatedData)
          .then(response => {
            console.log('Response:', response.data);
            this.dialog = false;
            this.getData();
          })
          .catch(error => {
            console.error('Error:', error);
          });
      },
  
      closeDialog() {
        this.dialog = false;
      },
    },
    mounted() {
      this.getData();
    }
  }
  </script>
  
<template>
  <v-container>
    <v-btn color="primary" @click="addUserFrom">
      <v-icon left>mdi-plus</v-icon>
      Add
    </v-btn>


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
          <v-btn icon @click="deleteItem(item)">
            <v-icon>mdi-delete</v-icon>
          </v-btn>
        </template>
      </v-data-table>
    </v-card>
  </v-container>

  <div class="text-center pa-4">
    <v-dialog v-model="dialog" max-width="600" persistent>
      <v-card>
        <v-card-title>
          <span class="headline">{{ formTitle }}</span>
        </v-card-title>

        <v-card-text>
          <v-sheet class="mx-auto" max-width="500">
            <v-form @submit.prevent="btnValue === 'Save' ? saveForm() : updateForm()">
              <v-text-field v-model="showData.name" :rules="firstNameRules" label="Full Name" outlined
                dense></v-text-field>
              <v-text-field v-model="showData.email" :rules="emailAddressRules" label="Email Address" outlined
                dense></v-text-field>
              <v-text-field v-model="showData.phone" :rules="phoneNumberRules" label="Phone Number" outlined
                dense></v-text-field>
              <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn @click="closeDialog" text>Close</v-btn>
                <v-btn type="submit" color="primary">{{ btnValue }}</v-btn>
              </v-card-actions>
            </v-form>
          </v-sheet>
        </v-card-text>
      </v-card>
    </v-dialog>
  </div>

</template>

<script>
import axios from 'axios'
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
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
      frontName: [],
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
          this.frontName = value;
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



    validateForm() {
      const nameValid = this.firstNameRules.every(rule => rule(this.showData.name) === true);
      const emailValid = this.emailAddressRules.every(rule => rule(this.showData.email) === true);
      const phoneValid = this.phoneNumberRules.every(rule => rule(this.showData.phone) === true);

      return nameValid && emailValid && phoneValid;
    },




    saveForm() {
      if (!this.validateForm()) {
        console.log(this.validateForm())
        return;
      }

      const saveData = {
        name: this.showData.name,
        email: this.showData.email,
        phone: this.showData.phone
      };

      console.log('Saving', saveData);

      axios.post('http://127.0.0.1:8000/api/store/user', saveData)
        .then(response => {
          toast.success('Data saved successfully!', {
            autoClose: 2000
          });
          setTimeout(() => {
            location.reload();
          }, 2000);

        })
        .catch(error => {
          console.error('Error:', error);
        });
    },



    updateForm() {
      if (!this.validateForm()) {
        return;
      }

      const updatedData = {
        name: this.showData.name,
        email: this.showData.email,
        phone: this.showData.phone
      };

      axios.post(`http://127.0.0.1:8000/api/update/user/${this.userData.id}`, updatedData)
        .then(response => {
          this.dialog = false;
          location.reload();
        })
        .catch(error => {
          console.error('Error:', error);
        });
    },



    editItem(item) {
      this.dialog = true;
      this.btnValue = "Update";
      this.formTitle = "Edit user";
      this.userData = item;
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


    deleteItem(item) {
      if (confirm('Are you sure you want to delete this item?')) {
        axios.delete(`http://127.0.0.1:8000/api/delete/user/${item.id}`)
          .then(response => {


            this.dialog = false;

            toast.success('Record delete successfully!', {
              autoClose: 2000,
              theme:'dark'
            });


            setTimeout(() => {
              location.reload();
            }, 2000);

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
  },


  mounted() {
    this.getData();
  }
}
</script>

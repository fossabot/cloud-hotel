<template>
  <v-dialog v-model="dialog" persistent max-width="600px">
    <v-tooltip v-if="edit === false" slot="activator" bottom>
      <v-btn icon flat slot="activator">
        <v-icon>add</v-icon>
      </v-btn>
      <span>Add Room Type</span>
    </v-tooltip>
    <v-tooltip v-if="edit === true" slot="activator" bottom>
      <v-btn icon flat slot="activator">
        <v-icon>edit</v-icon>
      </v-btn>
      <span>Edit Room Type</span>
    </v-tooltip>
    <v-card>
      <v-card-text>
        <v-text-field
          v-model.trim="form.name"
          placeholder="Name"
          :error-messages="formError.name"
          :error="formError.name.length > 0"
        />
      </v-card-text>
      <v-divider/>
      <v-card-actions>
        <v-btn v-if="edit === false" :disabled="loading" @click.prevent.stop="submit" color="primary">
          Submit
        </v-btn>
        <v-btn v-if="edit === true" :disabled="loading" @click.prevent.stop="update" color="primary">
          Update
        </v-btn>
        <v-btn :disabled="loading" @click.prevent.stop="dialog=!dialog">
          Cancel
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
  export default {
    name: "RoomTypeAddForm",
    props: {
      roomType: {
        type: Object,
        default: () => ({})
      }
    },
    data: () => ({
      loading: false,
      form: {
        name: "",
      },
      formError: {
        name: []
      },
      edit: false,
      dialog: false
    }),
    created() {
      if(Object.keys(this.roomType).length > 0) {
        this.form = {
          name: this.roomType.name
        }
        this.edit = true;
      }
    },
    methods: {
      async submit() {
        try {
          this.loading = true;
          this.formError = {
            name: []
          }
          await this.$axios.post('/room-types', this.form);
          this.$emit('refresh');
          this.$toast.success("Room type create successful")
          this.dialog = false;
          this.loading = false;
          this.form = {
            name: ''
          };
          this.formError.name = [];
        } catch (e) {
          if (e.response) {
            const code = parseInt(e.response.status);
            if (!!code && code === 422) {
              const data = e.response.data;
              if (data) {
                const errors = data.errors;
                if (errors) {
                  this.formError = {
                    ...this.formError,
                    ...errors
                  }
                }
              }

            }
          }
        } finally {
          this.loading = false
        }
      },
      async update() {
        try {
          this.loading = true;
          this.formError = {
            name: [],
            permissions: []
          }
          await this.$axios.$put('/room-types/' + this.roomType.id, this.form);
          this.$emit('refresh');
          this.dialog = false;
          this.loading = false;
          this.form = {
            name: ''
          };
          this.formError.name = [];
          this.$toast.success("Update successful")
        } catch (e) {
          if (e.response) {
            const code = parseInt(e.response.status);
            if (!!code && code === 422) {
              const data = e.response.data;
              if (data) {
                const errors = data.errors;
                if (errors) {
                  this.formError = {
                    ...this.formError,
                    ...errors
                  }
                }
              }
            }
          }
        } finally {
          this.loading = false
        }
      }
    }
  }
</script>

<style scoped>

</style>

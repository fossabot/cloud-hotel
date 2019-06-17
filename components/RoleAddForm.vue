<template>
  <v-card>
    <v-card-text>
      <v-text-field
        v-model.trim="form.name"
        placeholder="Name"
        :error-messages="formError.name"
        :error="formError.name.length > 0"
      />
      <PermissionsSelect
        v-model="form.permissions"
        placeholder="Permissions"
        multiple
        :error-messages="formError.permissions"
        :error="formError.permissions.length > 0"
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
    </v-card-actions>
  </v-card>
</template>

<script>
  import RoleSelect from "./RoleSelect";
  import PermissionsSelect from "./PermissionsSelect";

  export default {
    name: "RoleAddForm",
    components: {PermissionsSelect, RoleSelect},
    props: {
      role: {
        type: Object,
        default: () => ({})
      }
    },
    data: () => ({
      loading: false,
      form: {
        name: "",
        permissions: []
      },
      formError: {
        name: [],
        permissions: []
      },
      edit: false
    }),
    watch: {
      role(e) {
        if (Object.keys(e).length > 0) {
          this.form = {
            ...this.form,
            name: e.name,
            permissions: e.permissions
          }
          this.edit = true
        }
      }
    },
    methods: {
      async submit() {
        try {
          this.loading = true;
          this.formError = {
            name: [],
            permissions: []
          }
          await this.$axios.post('/roles', this.form)
          this.$router.push('/roles')
          this.$toast.success("Role create successful")
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
          await this.$axios.$put('/roles/' + this.role.id, this.form);
          this.$router.push('/roles/edit/' + this.role.id)
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

<template>
  <v-card>
    <v-card-text>
      <v-layout row>
        <v-flex class="pr-3" sm6>
          <v-text-field
            v-model.trim="form.firstName"
            placeholder="First Name"
            :error-messages="formError.firstName"
            :error="formError.firstName.length > 0"
          />
        </v-flex>
        <v-flex class="pl-3" sm6>
          <v-text-field
            v-model.trim="form.lastName"
            placeholder="Last Name"
            :error-messages="formError.lastName"
            :error="formError.lastName.length > 0"
          />
        </v-flex>
      </v-layout>
      <RoleSelect
        placeholder="Roles"
        v-model="form.roles"
        :error-messages="formError.roles"
        :error="formError.roles.length > 0"
      />
      <v-layout row>
        <v-flex class="pr-3" sm6>
          <v-text-field
            v-model.trim="form.email"
            placeholder="Email Address"
            :error-messages="formError.email"
            :error="formError.email.length > 0"
          />
        </v-flex>
        <v-flex class="pl-3" sm6>
          <v-text-field
            v-model.trim="form.password"
            placeholder="Password"
            :error-messages="formError.password"
            :error="formError.password.length > 0"
          />
        </v-flex>
      </v-layout>
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

  export default {
    name: "UserAddForm",
    components: {RoleSelect},
    props: {
      user: {
        type: Object,
        default: () => ({})
      }
    },
    data: () => ({
      loading: false,
      form: {
        email: "",
        password: "",
        firstName: "",
        lastName: "",
        roles: ''
      },
      formError: {
        email: [],
        password: [],
        firstName: [],
        lastName: [],
        roles: []
      },
      edit: false
    }),
    watch: {
      user(e) {
        if (Object.keys(e).length > 0) {
          this.form = {
            ...this.form,
            email: e.email,
            firstName: e.metas.firstName,
            lastName: e.metas.lastName,
            roles: e.roles.id,
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
            email: [],
            password: [],
            firstName: [],
            lastName: [],
            roles: []
          }
          await this.$axios.post('/users', this.form)
          this.$router.push('/users')
          this.$toast.success("User create successful")
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
            email: [],
            password: [],
            firstName: [],
            lastName: [],
            roles: []
          }
          await this.$axios.$put('/users/' + this.user.id, this.form);
          this.$router.push('/users/edit/' + this.user.id)
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

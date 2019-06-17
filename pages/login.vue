<template>
  <v-flex sm6 md4 lg4 xlg3>
    <v-card>
      <v-form class="pa-4">
        <v-text-field
          placeholder="Email Address"
          autofocus
          v-model.trim="form.email"
          :error-messages="formError.email"
          :error="formError.email.length > 0"
        />
        <v-text-field
          v-model.trim="form.password"
          :append-icon="show1 ? 'visibility' : 'visibility_off'"
          :type="show1 ? 'text' : 'password'"
          placeholder="Password"
          hint="At least 8 characters"
          @click:append="show1 = !show1"
          :error-messages="formError.password"
          :error="formError.password.length > 0"
        />
        <v-btn
          @click.prevent="submit"
          :disabled="loading"
          primary> Submit
        </v-btn>
      </v-form>
    </v-card>
  </v-flex>
</template>

<script>
  export default {
    layout: "auth",
    name: "login",
    data: () => ({
      loading: false,
      show1: false,
      form: {
        email: '',
        password: ''
      },
      formError: {
        email: [],
        password: []
      }
    }),
    methods: {
      async submit() {
        try {
          this.loading = true;
          this.formError = {
            email: [],
            password: []
          };
          const {data: {results: {token, expire}}} = await this.$axios.post('/login', this.form);

          const date = new Date(expire * 1000);
          document.cookie = `token=${token}; expires=${date.toUTCString()}; path=/`;

          this.$router.push('/');
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

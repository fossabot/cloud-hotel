<template>
  <v-card>
    <v-card-title>
      <h3>Hotel Info</h3>
    </v-card-title>
    <v-divider/>
    <v-card-text>
      <v-layout row>
        <v-flex sm6 class="pr-3">
          <Upload v-model="form.image" :_preview="form.image" :accept="['image/jpeg', 'image/png']"/>
        </v-flex>
        <v-flex sm6 class="pl-3">
          <v-text-field
            v-model.trim="form.name"
            placeholder="Name"
            :error-messages="formError.name"
            :error="formError.name.length > 0"
          />
          <v-text-field
            v-model.trim="form.slug"
            placeholder="Slug"
            :error-messages="formError.slug"
            :error="formError.slug.length > 0"
          />
          <v-text-field
            v-model.trim="form.email"
            placeholder="Email Address"
            :error-messages="formError.email"
            :error="formError.email.length > 0"
          />
          <v-text-field
            v-model.trim="form.phone"
            placeholder="Phone"
            :error-messages="formError.phone"
            :error="formError.phone.length > 0"
          />
        </v-flex>
      </v-layout>
      <v-layout row>
        <v-flex sm6 class="pr-3">
          <v-textarea
            v-model.trim="form.address"
            placeholder="Address"
            auto-grow
            :rows="10"
            :error-messages="formError.address"
            :error="formError.address.length > 0"
          />
        </v-flex>
        <v-flex sm6 class="pl-3">
          <v-text-field
            v-model.trim="form.state"
            placeholder="State"
            :error-messages="formError.state"
            :error="formError.state.length > 0"
          />
          <v-text-field
            v-model.trim="form.zipCode"
            placeholder="ZipCode"
            :error-messages="formError.zipCode"
            :error="formError.zipCode.length > 0"
          />
          <v-text-field
            v-model.trim="form.city"
            placeholder="City"
            :error-messages="formError.city"
            :error="formError.city.length > 0"
          />
          <v-text-field
            v-model.trim="form.country"
            placeholder="Country"
            :error-messages="formError.country"
            :error="formError.country.length > 0"
          />
        </v-flex>
      </v-layout>
    </v-card-text>
    <v-divider/>
    <div v-if="!edit">
      <v-card-title>
        <h3>Hotel Manager Info</h3>
      </v-card-title>
      <v-divider/>
      <v-card-text>
        <v-layout row>
          <v-flex class="pr-3" sm6>
            <v-text-field
              v-model.trim="form.account_firstName"
              placeholder="First Name"
              :error-messages="formError.account_firstName"
              :error="formError.account_firstName.length > 0"
            />
          </v-flex>
          <v-flex class="pl-3" sm6>
            <v-text-field
              v-model.trim="form.account_lastName"
              placeholder="Last Name"
              :error-messages="formError.account_lastName"
              :error="formError.account_lastName.length > 0"
            />
          </v-flex>
        </v-layout>
        <RoleSelect
          placeholder="Roles"
          v-model="form.role"
          :error-messages="formError.role"
          :error="formError.role.length > 0"
        />
        <v-layout row>
          <v-flex class="pr-3" sm6>
            <v-text-field
              v-model.trim="form.account_email"
              placeholder="Email Address"
              :error-messages="formError.account_email"
              :error="formError.account_email.length > 0"
            />
          </v-flex>
          <v-flex class="pl-3" sm6>
            <v-text-field
              v-model.trim="form.account_password"
              placeholder="Password"
              :error-messages="formError.account_password"
              :error="formError.account_password.length > 0"
            />
          </v-flex>
        </v-layout>
      </v-card-text>
    </div>
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
  import Upload from "./Upload";

  export default {
    name: "HotelAddForm",
    components: {Upload, RoleSelect},
    props: {
      hotel: {
        type: Object,
        default: () => ({})
      }
    },
    data: () => ({
      loading: false,
      form: {
        email: "",
        name: "",
        slug: "",
        address: "",
        city: "",
        state: "",
        country: "",
        zipCode: "",
        phone: "",
        image: '',
        account_email: "",
        account_password: "",
        account_firstName: "",
        account_lastName: "",
        role: ''
      },
      formError: {
        email: [],
        name: [],
        slug: [],
        address: [],
        city: [],
        state: [],
        country: [],
        zipCode: [],
        phone: [],
        image: [],
        account_email: [],
        account_password: [],
        account_firstName: [],
        account_lastName: [],
        role: [],
      },
      edit: false
    }),
    watch: {
      hotel(e) {
        if (Object.keys(e).length > 0) {
          this.form = {
            ...this.form,
            email: e.email,
            name: e.name,
            slug: e.slug,
            address: e.metas.address,
            city: e.metas.city,
            state: e.metas.state,
            country: e.metas.country,
            zipCode: e.metas.zipCode,
            phone: e.metas.phone,
            image: e.image,
          }
          this.edit = true
        }
      }
    },
    destroyed() {
      this.loading = false;
      this.formError = {
        email: [],
        name: [],
        slug: [],
        address: [],
        city: [],
        state: [],
        country: [],
        zipCode: [],
        phone: [],
        image: [],
        permissions: [],
        account_email: [],
        account_password: [],
        account_firstName: [],
        account_lastName: [],
        role: [],
      }
      this.form = {
        email: "",
        name: "",
        slug: "",
        address: "",
        city: "",
        state: "",
        country: "",
        zipCode: "",
        phone: "",
        image: '',
        account_email: "",
        account_password: "",
        account_firstName: "",
        account_lastName: "",
        role: ''
      }
    },
    methods: {
      async submit() {
        try {
          this.loading = true;
          this.formError = {
            email: [],
            name: [],
            slug: [],
            address: [],
            city: [],
            state: [],
            country: [],
            zipCode: [],
            phone: [],
            image: [],
            permissions: [],
            account_email: [],
            account_password: [],
            account_firstName: [],
            account_lastName: [],
            role: [],
          }
          let form = new FormData();

          Object.keys(this.form)
            .forEach(e => form.append(e, this.form[e]))
          await this.$axios.post('/hotels', form)
          this.$router.push('/hotels')
          this.$toast.success("Hotel create successful")
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
            name: [],
            slug: [],
            address: [],
            city: [],
            state: [],
            country: [],
            zipCode: [],
            phone: [],
            image: [],
            permissions: [],
            account_email: [],
            account_password: [],
            account_firstName: [],
            account_lastName: [],
            role: [],
          }
          let form = new FormData();

          Object.keys(this.form)
            .forEach(e => {
              if (e === "image" && this.form[e] === this.hotel.image) {
                return null
              } else {
                form.append(e, this.form[e])
              }
            });
          await this.$axios.$post('/hotels/' + this.hotel.id, form);
          this.$router.push('/hotels/edit/' + this.hotel.id)
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

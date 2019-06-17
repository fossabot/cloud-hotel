<template>
  <v-layout column>
    <v-flex>
      <v-progress-linear :indeterminate="true" v-if="loading"/>
      <UserAddForm :user="user"/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import UserAddForm from "~/components/UserAddForm"

  export default {
    name: "UserAdd",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    async beforeRouteEnter(from, to, next) {
      await next(async vm => {
        if (!vm.permissions.includes('user_update')) {
          vm.$router.push('/400')
        }
        vm.findUser(from.params)
      })
    },
    data: () => ({
      user: {},
      loading: false
    }),
    components: {UserAddForm},
    methods: {
      async findUser({id}) {
        try {
          this.loading = true;
          const {data: {results}} = await this.$axios.get('/users/' + id)
          this.user = results
        } catch (e) {
          console.log(e)
        } finally {
          this.loading = false
        }
      }
    }
  }
</script>

<style scoped>

</style>

<template>
  <v-layout column>
    <v-flex>
      <v-progress-linear :indeterminate="true" v-if="loading"/>
      <RoleAddForm :role="role"/>
    </v-flex>
  </v-layout>
</template>

<script>
  import {mapGetters} from "vuex"
  import RoleAddForm from "~/components/RoleAddForm"

  export default {
    name: "RoleAdd",
    computed: {
      ...mapGetters({
        permissions: "init/PERMISSIONS"
      })
    },
    async beforeRouteEnter(from, to, next) {
      await next(async vm => {
        if (!vm.permissions.includes('role_update')) {
          vm.$router.push('/400')
        }
        vm.findRole(from.params)
      })
    },
    data: () => ({
      role: {},
      loading: false
    }),
    components: {RoleAddForm},
    methods: {
      async findRole({id}) {
        try {
          this.loading = true;
          const {data: {results}} = await this.$axios.get('/roles/' + id)
          this.role = results
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

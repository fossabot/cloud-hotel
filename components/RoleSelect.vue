<template>
  <v-select
    :loading="loading"
    v-bind="$attrs"
    :items="roles"
    item-text="name"
    item-value="id"
    @change="e => $emit('input', e)"
  />
</template>

<script>
  export default {
    name: "RoleSelect",
    data: () => ({
      roles: [],
      loading: false,
    }),
    async created() {
      try {
        this.loading = true;
        let url = '/roles'
        const {data:{results}} = await this.$axios.get(url);
        this.roles = results;
      } finally {
        this.loading = false
      }
    }
  }
</script>

<style scoped>

</style>

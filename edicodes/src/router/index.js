import { createRouter, createWebHistory } from 'vue-router'
import Home from '@/views/Home.vue'
import NotFound from '@/views/NotFound.vue'
import DonationSuccess from '@/views/DonationSuccess.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home
    },
    {
      path: '/donation',
      name: 'donation',
      component: () => import('@/views/Donate.vue')
    },
    {
      path: '/blog',
      name: 'blog',
      component: () => import('@/views/Blog.vue')
    },
    {
      path: '/blog/:slug',
      name: 'post',
      component: () => import('@/views/Post.vue')
    },
    {
      path: '/donation/success',
      name: 'donation-success',
      component: DonationSuccess
    },
    // Admin routes
    {
      path: '/admin/login',
      name: 'admin-login',
      component: () => import('@/views/admin/Login.vue')
    },
    {
      path: '/admin',
      name: 'admin',
      component: () => import('@/views/admin/AdminLayout.vue'),
      meta: { requiresAuth: true },
      children: [
        {
          path: '',
          name: 'admin-dashboard',
          component: () => import('@/views/admin/Dashboard.vue')
        },
        {
          path: 'analytics',
          name: 'admin-analytics',
          component: () => import('@/views/admin/analytics/Index.vue')
        },
        {
          path: 'posts',
          name: 'admin-posts',
          component: () => import('@/views/admin/posts/PostsList.vue')
        },
        {
          path: 'posts/create',
          name: 'admin-posts-create',
          component: () => import('@/views/admin/posts/PostForm.vue')
        },
        {
          path: 'posts/edit/:id',
          name: 'admin-posts-edit',
          component: () => import('@/views/admin/posts/PostForm.vue'),
          props: true
        },
        {
          path: 'comments',
          name: 'admin-comments',
          component: () => import('@/views/admin/comments/CommentsList.vue')
        },
        {
          path: 'comments/:id',
          name: 'admin-comments-detail',
          component: () => import('@/views/admin/comments/CommentDetail.vue'),
          props: true
        },
        {
          path: 'categories',
          name: 'admin-categories',
          component: () => import('@/views/admin/categories/CategoriesList.vue')
        },
        {
          path: 'categories/create',
          name: 'admin-categories-create',
          component: () => import('@/views/admin/categories/CategoryForm.vue')
        },
        {
          path: 'categories/edit/:id',
          name: 'admin-categories-edit',
          component: () => import('@/views/admin/categories/CategoryForm.vue'),
          props: true
        },
        {
          path: 'donations',
          name: 'admin-donations',
          component: () => import('@/views/admin/donations/DonationsList.vue')
        },
        {
          path: 'settings',
          name: 'admin-settings',
          component: () => import('@/views/admin/settings/Index.vue')
        }
      ]
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: NotFound
    }
  ],
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition
    } else if (to.hash) {
      return {
        el: to.hash,
        behavior: 'smooth'
      }
    } else {
      return { top: 0 }
    }
  }
})

// Navigation guard to check authentication
router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAuth)) {
    // This route requires auth, check if logged in
    const isAuthenticated = localStorage.getItem('admin_token')
    if (!isAuthenticated) {
      next({ name: 'admin-login', query: { redirect: to.fullPath } })
    } else {
      next()
    }
  } else {
    next()
  }
})

export default router 
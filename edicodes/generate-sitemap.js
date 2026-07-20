import fs from 'fs'
import path from 'path'
import { fileURLToPath } from 'url'
import { SitemapStream, streamToPromise } from 'sitemap'
import { Readable } from 'stream'
import axios from 'axios'

const __filename = fileURLToPath(import.meta.url)
const __dirname = path.dirname(__filename)
const envPath = path.join(__dirname, '.env')

if (fs.existsSync(envPath)) {
  const envContents = fs.readFileSync(envPath, 'utf8')
  envContents.split(/\r?\n/).forEach((line) => {
    const match = line.match(/^\s*([\w.-]+)\s*=\s*(.*)?\s*$/)
    if (!match) return

    const key = match[1]
    let value = match[2] || ''

    if (value.startsWith('"') && value.endsWith('"')) {
      value = value.slice(1, -1)
    } else if (value.startsWith("'") && value.endsWith("'")) {
      value = value.slice(1, -1)
    }

    value = value.trim()

    if (process.env[key] === undefined) {
      process.env[key] = value
    }
  })
}

const rawApiUrl =
  process.env.VITE_API_BASE_URL || process.env.VITE_API_URL || 'http://localhost:8000/api'
const API_URL = rawApiUrl.replace(/\/$/, '')

async function fetchPostsList() {
  try {
    const response = await axios.get(`${API_URL}/posts`)
    return response.data.data || []
  } catch (error) {
    console.error('Error fetching posts:', error?.response?.data || error)
    return []
  }
}

async function fetchCategoriesList() {
  try {
    const response = await axios.get(`${API_URL}/categories`)
    return response.data.data || []
  } catch (error) {
    console.error('Error fetching categories:', error?.response?.data || error)
    return []
  }
}

async function fetchCoursesList() {
  try {
    const response = await axios.get(`${API_URL}/courses`)
    return response.data.data || []
  } catch (error) {
    console.error('Error fetching courses:', error?.response?.data || error)
    return []
  }
}

async function generateSitemap() {
  try {
    // Fetch data from the API
    const posts = await fetchPostsList()
    const categories = await fetchCategoriesList()
    // const courses = await fetchCoursesList();

    // Define static routes
    const staticRoutes = [
      { url: '/', changefreq: 'daily', priority: 1.0 },
      { url: '/blog', changefreq: 'daily', priority: 0.9 },
      { url: '/contact', changefreq: 'monthly', priority: 0.8 },
      // { url: '/courses', changefreq: 'weekly', priority: 0.8 },
    ]

    // Create links array with all routes
    const links = [
      ...staticRoutes,
      // Add blog categories
      ...categories.map((category) => ({
        url: `/blog/category/${category.slug}`,
        changefreq: 'weekly',
        priority: 0.6,
      })),
      // Add blog posts
      ...posts.map((post) => ({
        url: `/blog/${post.slug}`,
        changefreq: 'weekly',
        priority: 0.7,
        lastmod: post.updated_at || post.published_at,
      })),
      // Add courses
      // ...courses.map(course => ({
      //   url: `/courses/${course.slug}`,
      //   changefreq: 'weekly',
      //   priority: 0.7,
      //   lastmod: course.updated_at || course.created_at
      // }))
    ]

    // Create a stream to write to
    const stream = new SitemapStream({ hostname: 'https://edrisranjbar.ir' })

    // Return a promise that resolves with your XML string
    const sitemap = await streamToPromise(Readable.from(links).pipe(stream))

    // Write the XML to public directory
    fs.writeFileSync('./public/sitemap.xml', sitemap.toString())

    console.log('Sitemap generated successfully ✅')
  } catch (error) {
    console.error('Error generating sitemap:', error)
  }
}

// Run the function
generateSitemap()

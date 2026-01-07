import fs from 'fs';
import { SitemapStream, streamToPromise } from 'sitemap';
import { Readable } from 'stream';
import axios from 'axios';

const API_URL = process.env.VITE_API_URL || 'http://localhost:8000';

async function fetchPostsList() {
  try {
    const response = await axios.get(`${API_URL}/api/posts`);
    return response.data.data || [];
  } catch (error) {
    console.error('Error fetching posts:', error);
    return [];
  }
}

async function fetchCategoriesList() {
  try {
    const response = await axios.get(`${API_URL}/api/categories`);
    return response.data.data || [];
  } catch (error) {
    console.error('Error fetching categories:', error);
    return [];
  }
}

async function fetchCoursesList() {
  try {
    const response = await axios.get(`${API_URL}/api/courses`);
    return response.data.data || [];
  } catch (error) {
    console.error('Error fetching courses:', error);
    return [];
  }
}

async function generateSitemap() {
  try {
    // Fetch data from the API
    const posts = await fetchPostsList();
    const categories = await fetchCategoriesList();
    const courses = await fetchCoursesList();

    // Define static routes
    const staticRoutes = [
      { url: '/', changefreq: 'daily', priority: 1.0 },
      { url: '/blog', changefreq: 'daily', priority: 0.9 },
      { url: '/courses', changefreq: 'weekly', priority: 0.8 },
    ];

    // Create links array with all routes
    const links = [
      ...staticRoutes,
      // Add blog categories
      ...categories.map(category => ({
        url: `/blog/category/${category.slug}`,
        changefreq: 'weekly',
        priority: 0.6
      })),
      // Add blog posts
      ...posts.map(post => ({
        url: `/blog/${post.slug}`,
        changefreq: 'weekly',
        priority: 0.7,
        lastmod: post.updated_at || post.published_at
      })),
      // Add courses
      ...courses.map(course => ({
        url: `/courses/${course.slug}`,
        changefreq: 'weekly',
        priority: 0.7,
        lastmod: course.updated_at || course.created_at
      }))
    ];

    // Create a stream to write to
    const stream = new SitemapStream({ hostname: 'https://edrisranjbar.ir' });

    // Return a promise that resolves with your XML string
    const sitemap = await streamToPromise(Readable.from(links).pipe(stream));
    
    // Write the XML to public directory
    fs.writeFileSync('./public/sitemap.xml', sitemap.toString());
    
    console.log('Sitemap generated successfully ✅');
  } catch (error) {
    console.error('Error generating sitemap:', error);
  }
}

// Run the function
generateSitemap(); 
/**
 * Global money formatting utility for the entire application
 * Handles both display formatting and input parsing
 */

// Format price for display (with تومان suffix)
export const formatMoney = (price) => {
  if (!price || price === 0) return '0 تومان';
  const priceStr = price.toString();
  const parts = priceStr.split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return parts.join('.') + ' تومان';
};

// Format price for display (without تومان suffix) - for input fields
export const formatMoneyInput = (price) => {
  if (!price || price === 0) return '0';
  const priceStr = price.toString();
  const parts = priceStr.split('.');
  // Ensure we're working with a valid number string
  if (parts[0] && /^\d+$/.test(parts[0])) {
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  }
  return parts.join('.');
};

// Parse formatted price back to number (from input fields)
export const parseMoneyInput = (formattedPrice) => {
  if (!formattedPrice || formattedPrice === '0') return 0;

  // Remove commas and any other non-digit characters except Persian digits
  const cleaned = formattedPrice.toString().replace(/[^\d\u06F0-\u06F9]/g, '');

  // Convert Persian digits to English digits
  const persianToEnglish = {
    '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
    '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
  };

  let result = '';
  for (let char of cleaned) {
    result += persianToEnglish[char] || char;
  }

  // Ensure we have a valid number
  const parsed = parseInt(result);
  return isNaN(parsed) ? 0 : parsed;
};

// Format price for labels and display purposes
export const formatPriceLabel = (price) => {
  if (!price || price === 0) return 'رایگان';
  return formatMoney(price);
};

// Format price for tables and lists (compact)
export const formatPriceCompact = (price) => {
  if (!price || price === 0) return '0';
  const priceStr = price.toString();
  const parts = priceStr.split('.');
  parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
  return parts.join('.');
};

// Check if a price is free
export const isFree = (price) => {
  return !price || price === 0;
};

// Get currency symbol
export const getCurrencySymbol = () => 'تومان';

// Get currency name
export const getCurrencyName = () => 'تومان';

// Convert storage path to full URL
export const getStorageUrl = (path) => {
  if (!path) return null;

  // If it's already a full URL, return as is
  if (path.startsWith('http://') || path.startsWith('https://')) {
    return path;
  }

  // Remove leading slash if present
  const cleanPath = path.startsWith('/') ? path.substring(1) : path;

  // Construct full URL
  const baseUrl = import.meta.env.VITE_API_BASE_URL || 'http://localhost:8000';
  return `${baseUrl}/storage/${cleanPath}`;
};

// Get thumbnail URL for course
export const getCourseThumbnailUrl = (course) => {
  if (!course) return null;

  // Check if course has a thumbnail_url property (from backend accessor)
  if (course.thumbnail_url) {
    return course.thumbnail_url;
  }

  // Check if course has a thumbnail property
  if (course.thumbnail) {
    return getStorageUrl(course.thumbnail);
  }

  return null;
};

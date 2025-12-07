import { useState, useEffect } from 'react';

export default function OptimizedImage({ images = {}, size = 'medium', alt = '', className = '' }) {
  const jpg = images?.[size] || images?.large || images?.original;
  const webp = images?.[`${size}_webp`] || images?.large_webp;
  const [blur, setBlur] = useState(true);

  useEffect(() => {
    const img = new Image();
    img.src = jpg;
    img.onload = () => setBlur(false);
  }, [jpg]);

  const width = size === 'thumbnail' ? 300 : size === 'medium' ? 800 : 1200;
  const height = Math.round((9 / 16) * width); // rough aspect ratio to avoid CLS

  return (
    <picture>
      {webp && <source srcSet={webp} type="image/webp" />}
      <img
        src={jpg}
        srcSet={`${images?.thumbnail || jpg} 300w, ${images?.medium || jpg} 800w, ${images?.large || jpg} 1200w`}
        sizes="(max-width: 480px) 300px, (max-width: 1024px) 800px, 1200px"
        alt={alt}
        loading="lazy"
        width={width}
        height={height}
        className={className}
        style={{ filter: blur ? 'blur(8px)' : 'none', transition: 'filter 250ms ease', width: '100%', height: 'auto', borderRadius: 6 }}
      />
    </picture>
  );
}

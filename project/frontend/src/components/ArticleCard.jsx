import { useState, useEffect } from 'react';
import OptimizedImage from './OptimizedImage';
import CommentList from './CommentList';
import { getComments } from '../services/api';

function ArticleCard({ article, onDelete }) {
  const [showComments, setShowComments] = useState(false);
  const [commentsCount, setCommentsCount] = useState(article.comments_count || 0);

  // Charger le nombre de commentaires au montage du composant
  useEffect(() => {
    const fetchCommentsCount = async () => {
      try {
        const response = await getComments(article.id);
        setCommentsCount(response.data.length);
      } catch (error) {
        console.error('Error fetching comments count:', error);
      }
    };
    
    fetchCommentsCount();
  }, [article.id]);

  const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    
    const date = new Date(dateString);
    
    return date.toLocaleDateString('fr-FR', {
      year: 'numeric',
      month: '2-digit',
      day: '2-digit',
      hour: '2-digit',
      minute: '2-digit',
      timeZone: 'Europe/Paris'
    });
  };

  return (
    <div className="card">
      {article.images && (article.images.medium || article.image_url) && (
        (() => {
          const jpgMedium = article.images?.medium || article.image_url;
          const jpgLarge = article.images?.large || article.image_url;
          const webp = article.images?.medium_webp || null;
          const imgWidth = 600;
          const imgHeight = 400; // approximate aspect for layout stability
          return (
            <picture>
              {webp && <source srcSet={webp} type="image/webp" />}
              <img
                src={jpgMedium}
                srcSet={`${jpgMedium} 600w, ${jpgLarge} 1200w`}
                sizes="(max-width: 768px) 600px, 1200px"
                alt={article.title}
                className="article-image"
                loading="lazy"
                width={imgWidth}
                height={imgHeight}
                style={{ width: '100%', height: 'auto', marginBottom: '0.75rem', borderRadius: '6px' }}
              />
            </picture>
          );
        })()
      )}
      <h3>{article.title}</h3>
      {article.images && (
        <OptimizedImage images={article.images} size="medium" alt={article.title} />
      )}
      <div style={{ color: '#7f8c8d', fontSize: '0.9em', marginBottom: '0.5rem' }}>
        Par {article.author} • {formatDate(article.created_at)}
      </div>
      <p style={{ marginBottom: '1rem' }}>{article.content}</p>
      
      <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
        <button 
          onClick={() => setShowComments(!showComments)}
          style={{ fontSize: '0.9em' }}
        >
          {showComments ? 'Masquer' : 'Afficher'} commentaires ({commentsCount})
        </button>
        
        {onDelete && (
          <button 
            onClick={() => onDelete(article.id)}
            style={{ 
              backgroundColor: '#e74c3c',
              fontSize: '0.9em'
            }}
          >
            Supprimer
          </button>
        )}
      </div>

      {showComments && (
        <div style={{ marginTop: '1rem', borderTop: '1px solid #ecf0f1', paddingTop: '1rem' }}>
          <CommentList articleId={article.id} onCommentsLoaded={setCommentsCount} />
        </div>
      )}
    </div>
  );
}

export default ArticleCard;


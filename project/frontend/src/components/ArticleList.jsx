import { useState, useEffect } from 'react';
import ArticleCard from './ArticleCard';
import { getArticles, searchArticles, deleteArticle } from '../services/api';

function ArticleList({ searchQuery }) {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [loadTime, setLoadTime] = useState(null);
  const showPerformanceTest = false; // mode test désactivé par défaut

  useEffect(() => {
    fetchArticles(false);
  }, [searchQuery]);

  const fetchArticles = async () => {
    try {
      setLoading(true);
      setError(null);
      const startTime = performance.now();
      
      let response;
      if (searchQuery && searchQuery.trim() !== '') {
        response = await searchArticles(searchQuery);
        console.log("data are : ",response)
      } else {
        response = await getArticles();
      }
      
      const endTime = performance.now();
      const timeInMs = (endTime - startTime).toFixed(0);
      setLoadTime(timeInMs);
      setArticles(response.data);
    } catch (err) {
      setError('Erreur lors du chargement des articles');
      console.error('Error fetching articles:', err);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (!window.confirm('Êtes-vous sûr de vouloir supprimer cet article ?')) {
      return;
    }

    try {
      await deleteArticle(id);
      setArticles(articles.filter(a => a.id !== id));
    } catch (err) {
      alert('Erreur lors de la suppression de l\'article');
      console.error('Error deleting article:', err);
    }
  };

  if (loading) {
    return <div className="loading">⏳ Chargement des articles...</div>;
  }

  if (error) {
    return <div className="error">{error}</div>;
  }

  if (articles.length === 0) {
    return (
      <div className="card" style={{ textAlign: 'center', color: '#7f8c8d' }}>
        Aucun article trouvé
      </div>
    );
  }

  return (
    <div>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: '1rem' }}>
        <h2>Articles ({articles.length})</h2>
        <div style={{ display: 'flex', gap: '0.5rem', alignItems: 'center' }}>
          {loadTime && (
            <span style={{ fontSize: '0.9em', color: '#7f8c8d' }}>
              ⏱️ {loadTime}ms
            </span>
          )}
        </div>
      </div>

      <div>
        {articles.map(article => (
          <ArticleCard 
            key={article.id} 
            article={article}
            onDelete={handleDelete}
          />
        ))}
      </div>
    </div>
  );
}

export default ArticleList;


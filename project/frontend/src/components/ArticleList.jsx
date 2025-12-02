import { useState, useEffect } from 'react';
import ArticleCard from './ArticleCard';
import { getArticles, searchArticles, deleteArticle } from '../services/api';

function ArticleList({ searchQuery }) {
  const [articles, setArticles] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [loadTime, setLoadTime] = useState(null);
  const [showPerformanceTest, setShowPerformanceTest] = useState(false);

  useEffect(() => {
    fetchArticles(showPerformanceTest);
  }, [searchQuery, showPerformanceTest]);

  const fetchArticles = async (withPerformanceTest = false) => {
    try {
      setLoading(true);
      setError(null);
      const startTime = performance.now();
      
      let response;
      if (searchQuery && searchQuery.trim() !== '') {
        response = await searchArticles(searchQuery);
        console.log("data are : ",response)
      } else {
        response = await getArticles(withPerformanceTest);
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
          <button 
            onClick={() => setShowPerformanceTest(!showPerformanceTest)}
            style={{ 
              fontSize: '0.85em',
              padding: '0.5em 1em',
              backgroundColor: showPerformanceTest ? '#e67e22' : '#95a5a6'
            }}
          >
            {showPerformanceTest ? '⚠️ Mode Performance Test' : '🧪 Tester Performance'}
          </button>
        </div>
      </div>

      {showPerformanceTest && (
        <div style={{
          padding: '1rem',
          backgroundColor: '#fff3cd',
          borderRadius: '4px',
          marginBottom: '1rem',
          fontSize: '0.9em'
        }}>
          <strong>🐛 Test de performance (PERF-001) - MODE ACTIF</strong>
          <div style={{ marginTop: '0.5rem' }}>
            ⚠️ Un délai artificiel de 30ms par article simule le coût du problème N+1<br/>
            • Ouvrez la console navigateur (F12) → onglet Network<br/>
            • Ouvrez les logs Docker : <code>docker logs blog_backend -f</code><br/>
            • Observez le nombre de requêtes SQL (~101 requêtes pour 50 articles au lieu d'1)<br/>
            • Avec 50 articles × 30ms = ~1,5 seconde de chargement
          </div>
          {loadTime && (
            <div style={{ marginTop: '0.5rem', color: '#856404' }}>
              ⏱️ Temps de chargement : <strong>{loadTime}ms</strong> 
              {parseInt(loadTime) > 1000 ? ' 🚨 TRÈS LENT!' : parseInt(loadTime) > 500 ? ' ⚠️ LENT!' : ''}
            </div>
          )}
        </div>
      )}

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


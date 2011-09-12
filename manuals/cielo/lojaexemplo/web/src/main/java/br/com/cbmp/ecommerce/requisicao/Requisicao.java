package br.com.cbmp.ecommerce.requisicao;

import java.io.IOException;

import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.MultiThreadedHttpConnectionManager;
import org.apache.commons.httpclient.methods.PostMethod;
import org.apache.commons.lang.time.StopWatch;
import org.apache.log4j.Logger;

import br.com.cbmp.ecommerce.contexto.Destino;
import br.com.cbmp.ecommerce.resposta.Erro;
import br.com.cbmp.ecommerce.resposta.FalhaComunicaoException;
import br.com.cbmp.ecommerce.resposta.RequisicaoInvalidaException;
import br.com.cbmp.ecommerce.resposta.Resposta;
import br.com.cbmp.ecommerce.resposta.RespostaFactory;
import br.com.cbmp.ecommerce.resposta.Transacao;

public class Requisicao {
	
	private static final int _1_SEGUNDO = 1000;
	
	private static final int _CONNECTION_TIMEOUT = 10 * _1_SEGUNDO;

	private static final int _READ_TIMEOUT = 40 * _1_SEGUNDO;

	private static final Logger logger = Logger.getLogger(Requisicao.class);
	
	private Mensagem mensagem;
	
	private static HttpClient httpClient;
	
	static {
		httpClient = new HttpClient();
		httpClient.setHttpConnectionManager(new MultiThreadedHttpConnectionManager());
		httpClient.getHttpConnectionManager().getParams().setConnectionTimeout(_CONNECTION_TIMEOUT);
		httpClient.getHttpConnectionManager().getParams().setSoTimeout(_READ_TIMEOUT);		
	}

	public Requisicao(Mensagem mensagem) {
		this.mensagem = mensagem;
	}

	public Transacao enviarPara(Destino destino) throws FalhaComunicaoException {
		String mensagemXml = mensagem.toXml();
		
		PostMethod httpMethod = new PostMethod(destino.getUrl());
		httpMethod.addParameter("mensagem", mensagemXml);
		
		if (logger.isDebugEnabled()) {
			logger.debug("Destino: '" + destino.getUrl() + "'\nMensagem: \n" + mensagemXml);
		}
		
		try {
			StopWatch stopWatch = new StopWatch();
			stopWatch.start();
			
			httpClient.executeMethod(httpMethod);
			
			stopWatch.stop();
			String respostaXml = httpMethod.getResponseBodyAsString();
			
			if (logger.isDebugEnabled()) {
				logger.debug("Retorno [em " + stopWatch + ", id='" + mensagem.getId() + "']: \n" + respostaXml);
			}
			
			Resposta resposta = RespostaFactory.getInstance().criar(respostaXml);
			
			if (resposta instanceof Erro) {
				Erro erro = (Erro) resposta;
				throw new RequisicaoInvalidaException(erro);
			}
			
			Transacao transacao = (Transacao) resposta;
			
			return transacao;
		} 
		catch (HttpException e) {
			logger.error(e, e);
			throw new FalhaComunicaoException(e.getMessage());
		} 
		catch (IOException e) {
			logger.error(e, e);
			throw new FalhaComunicaoException(e.getMessage());
		}
		finally {
			httpMethod.releaseConnection();
		}
	}

	

}
